package dev.vlab.tweetsms.presentation.main;

import android.Manifest;
import android.annotation.SuppressLint;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.pm.ActivityInfo;
import android.content.pm.PackageManager;
import android.graphics.drawable.Drawable;
import android.os.Build;
import android.os.Bundle;
import android.provider.Settings;
import android.telephony.SubscriptionInfo;
import android.telephony.SubscriptionManager;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.Window;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.work.OneTimeWorkRequest;
import androidx.work.WorkManager;

import dev.vlab.tweetsms.R;
import dev.vlab.tweetsms.apiclient.ApiInterface;
import dev.vlab.tweetsms.helper.PusherOdk;
import dev.vlab.tweetsms.helper.UrlContainer;
import dev.vlab.tweetsms.presentation.auth.AccountLoginActivity;
import dev.vlab.tweetsms.presentation.auth.LoginActivity;
import dev.vlab.tweetsms.apiclient.RetrofitInstance;
import dev.vlab.tweetsms.service.SmsWorkManager;
import dev.vlab.tweetsms.helper.SharedPrefManager;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.List;

import es.dmoral.toasty.Toasty;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

public class MainActivity extends AppCompatActivity {
    Context context;
    String TAG = "TAG";
    public static final String SMS_SENT_ACTION = "dev.vlab.tweetsms.SMS_SENT_ACTION";
    public static final String SMS_DELIVERED_ACTION = "dev.vlab.tweetsms.SMS_DELIVERED_ACTION";
    WorkManager mWorkManager;

    Context ctx;

    TextView statusText;
    Button logoutButton;
    LinearLayout connectedLL;

    // Broadcast receiver for force disconnect
    private BroadcastReceiver forceDisconnectReceiver = new BroadcastReceiver() {
        @Override
        public void onReceive(Context context, Intent intent) {
            if ("dev.vlab.tweetsms.DEVICE_FORCE_DISCONNECT".equals(intent.getAction())) {
                String deviceId = intent.getStringExtra("deviceId");
                Log.i(TAG, "Received force disconnect broadcast for device: " + deviceId);
                
                runOnUiThread(() -> {
                    // Update UI to show disconnected state
                    connectedLL.setBackground(ContextCompat.getDrawable(MainActivity.this, R.drawable.bg_disconnect));
                    statusText.setText("Disconnected by Admin");
                    
                    // Show message to user
                    Toasty.warning(MainActivity.this, "Your device has been disconnected remotely. Please scan QR code to reconnect.", Toast.LENGTH_LONG).show();
                    
                    // Redirect to login activity after a short delay
                    statusText.postDelayed(() -> {
                        Intent loginIntent = new Intent(MainActivity.this, LoginActivity.class);
                        loginIntent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
                        startActivity(loginIntent);
                        finish();
                    }, 3000); // 3 second delay to show the message
                });
            }
        }
    };

    @SuppressLint({"QueryPermissionsNeeded", "SetTextI18n", "SourceLockedOrientationActivity"})
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        try {
            Log.d(TAG, "MainActivity onCreate started");
            
            ctx = getApplicationContext();
            setContentView(R.layout.activity_main);
            setRequestedOrientation(ActivityInfo.SCREEN_ORIENTATION_PORTRAIT);
            
            // Window setup with error handling
            try {
                Window window = this.getWindow();
                window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
                window.clearFlags(WindowManager.LayoutParams.FLAG_TRANSLUCENT_STATUS);

                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                    window.setStatusBarColor(ContextCompat.getColor(this, R.color.splash_screen_bg));
                    getWindow().setNavigationBarColor(ContextCompat.getColor(this, R.color.splash_screen_bg));
                }
            } catch (Exception e) {
                Log.e(TAG, "Error setting up window", e);
                // Continue without window customization if it fails
            }

            // UI initialization with error handling
            try {
                statusText = findViewById(R.id.status_text_id);
                connectedLL = findViewById(R.id.connected_ll);
                logoutButton = findViewById(R.id.logout);
                
                if (statusText == null || connectedLL == null || logoutButton == null) {
                    throw new RuntimeException("Failed to find required UI elements");
                }
                
                @SuppressLint("UseCompatLoadingForDrawables") Drawable drawable = getResources().getDrawable(R.drawable.logout);
                if (drawable != null) {
                    drawable.setBounds(0, 0, 30, 30);
                    logoutButton.setCompoundDrawables(drawable, null, null, null);
                }

                setInitialColor();
                Log.d(TAG, "UI elements initialized successfully");
            } catch (Exception e) {
                Log.e(TAG, "Error initializing UI elements", e);
                // Show error message and return to login
                showErrorAndReturnToLogin("Failed to initialize app interface");
                return;
            }

            // Click listeners setup
            try {
                connectedLL.setOnClickListener(view -> {
                    if (statusText.getText().toString().equalsIgnoreCase(connecting) || 
                        statusText.getText().toString().equalsIgnoreCase(disconnecting)) {
                        // Do nothing during connecting/disconnecting
                    } else {
                        changeOnOffStatus();
                    }
                });

                logoutButton.setOnClickListener(v -> {
                    // Create a confirmation dialog
                    new AlertDialog.Builder(MainActivity.this)
                            .setTitle("Confirm Logout")
                            .setMessage("Are you sure you want to logout?")
                            .setPositiveButton("Yes", (dialog, which) -> {
                                // Perform logout actions
                                SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
                                manager.setStatus("false");
                                logoutButton.setText("Logout....");
                                logout();
                                logoutButton.setEnabled(false);
                            })
                            .setNegativeButton("No", (dialog, which) -> {
                                dialog.dismiss();
                            })
                            .show();
                });
                Log.d(TAG, "Click listeners set up successfully");
            } catch (Exception e) {
                Log.e(TAG, "Error setting up click listeners", e);
                // Continue without click listeners if they fail
            }

            // Permission request
            try {
                requestPermissions(new String[]{
                        Manifest.permission.READ_SMS,
                        Manifest.permission.SEND_SMS,
                        Manifest.permission.RECEIVE_SMS,
                        Manifest.permission.READ_PHONE_STATE,
                        Manifest.permission.CHANGE_NETWORK_STATE,
                        Manifest.permission.POST_NOTIFICATIONS,
                }, 0);
                Log.d(TAG, "Permissions requested");
            } catch (Exception e) {
                Log.e(TAG, "Error requesting permissions", e);
                // Continue without permission request if it fails
            }

            // Debug SMS capabilities
            try {
                checkSmsCapabilities();
                Log.d(TAG, "SMS capabilities checked");
            } catch (Exception e) {
                Log.e(TAG, "Error checking SMS capabilities", e);
                // Continue without SMS capability check if it fails
            }
            
            Log.d(TAG, "MainActivity onCreate completed successfully");
        } catch (Exception e) {
            Log.e(TAG, "Critical error in MainActivity onCreate", e);
            showErrorAndReturnToLogin("Critical error starting app: " + e.getMessage());
        }
    }
    
    private void showErrorAndReturnToLogin(String errorMessage) {
        try {
            Log.e(TAG, "Showing error and returning to login: " + errorMessage);
            
            // Try to show toast if possible
            runOnUiThread(() -> {
                try {
                    Toast.makeText(MainActivity.this, errorMessage, Toast.LENGTH_LONG).show();
                } catch (Exception e) {
                    Log.e(TAG, "Error showing toast", e);
                }
            });
            
            // Wait a moment then return to login
            new android.os.Handler().postDelayed(() -> {
                try {
                    Intent intent = new Intent(MainActivity.this, LoginActivity.class);
                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
                    startActivity(intent);
                    finish();
                } catch (Exception e) {
                    Log.e(TAG, "Error returning to login", e);
                    // If we can't even return to login, try to restart the app
                    try {
                        Intent intent = new Intent(MainActivity.this, AccountLoginActivity.class);
                        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
                        startActivity(intent);
                        finish();
                    } catch (Exception e2) {
                        Log.e(TAG, "Critical error - cannot restart app", e2);
                    }
                }
            }, 3000);
        } catch (Exception e) {
            Log.e(TAG, "Error in showErrorAndReturnToLogin", e);
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == 0) {
            boolean allGranted = true;
            StringBuilder deniedPermissions = new StringBuilder();

            for (int i = 0; i < permissions.length; i++) {
                if (grantResults[i] != PackageManager.PERMISSION_GRANTED) {
                    allGranted = false;
                    if (deniedPermissions.length() > 0) {
                        deniedPermissions.append(", ");
                    }
                    deniedPermissions.append(permissions[i].substring(permissions[i].lastIndexOf('.') + 1));
                }
            }

            if (!allGranted) {
                Log.e(TAG, "Permissions denied: " + deniedPermissions.toString());
                Toasty.error(this, "SMS permissions required for the app to work properly", Toast.LENGTH_LONG).show();

                // Check specifically for SMS permissions
                if (ContextCompat.checkSelfPermission(this, Manifest.permission.SEND_SMS) != PackageManager.PERMISSION_GRANTED) {
                    Log.e(TAG, "SEND_SMS permission denied - SMS sending will not work");
                }
            } else {
                Log.i(TAG, "All permissions granted");
            }
        }

    }

    @Override
    protected void onResume() {
        super.onResume();
        // Register the force disconnect broadcast receiver
        IntentFilter filter = new IntentFilter("dev.vlab.tweetsms.DEVICE_FORCE_DISCONNECT");
        registerReceiver(forceDisconnectReceiver, filter);
        Log.d(TAG, "Force disconnect receiver registered");
    }

    @Override
    protected void onPause() {
        super.onPause();
        // Unregister the broadcast receiver
        try {
            unregisterReceiver(forceDisconnectReceiver);
            Log.d(TAG, "Force disconnect receiver unregistered");
        } catch (IllegalArgumentException e) {
            // Receiver was not registered
            Log.w(TAG, "Force disconnect receiver was not registered");
        }
    }

    @Override
    protected void onStart() {
        super.onStart();
        Log.e(TAG, "onStart: ");

        try {
            // Check if WorkManager is available
            if (mWorkManager == null) {
                // Initialize mWorkManager
                mWorkManager = WorkManager.getInstance(this);
                Log.d(TAG, "WorkManager initialized");
            }

            // Cancel all previous work
            mWorkManager.cancelAllWork();
            Log.d(TAG, "Previous work cancelled");

            // Call Pusher with error handling
            setupBackgroundService();
            Log.d(TAG, "Background service setup completed");
        } catch (Exception e) {
            Log.e(TAG, "onStart: error " + e.getMessage(), e);
            // Show error to user but don't crash
            try {
                if (statusText != null) {
                    runOnUiThread(() -> {
                        statusText.setText("Error starting services");
                        Toast.makeText(MainActivity.this, 
                            "Warning: Background services failed to start", 
                            Toast.LENGTH_LONG).show();
                    });
                }
            } catch (Exception uiError) {
                Log.e(TAG, "Error updating UI after onStart failure", uiError);
            }
        }
    }

    @SuppressLint("SetTextI18n")
    void setInitialColor() {


        SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
        String status = manager.getStatus();
        if (status.equalsIgnoreCase("true")) {

            connectedLL.setBackground(ContextCompat.getDrawable(MainActivity.this, R.drawable.bg_connect));
            statusText.setText("Connected");

        } else {
            connectedLL.setBackground(ContextCompat.getDrawable(MainActivity.this, R.drawable.bg_disconnect));
            statusText.setText("Disconnected");
        }


    }

    private void changeOnOffStatus() {

        SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
        String status = manager.getStatus();
        if (status.equalsIgnoreCase("true")) {
            logout();
        } else {
            loginUserWithQr(manager.getQrData());
            manager.setStatus("true");
        }
    }

    String connecting = "Connecting...";
    String disconnecting = "Disconnecting...";

    private void loginUserWithQr(String scannedData) {

        statusText.setText(connecting);
        setDeviceInfoData();
        Retrofit retrofit = RetrofitInstance.getRetrofitInstance(UrlContainer.getBaseUrl());
        ApiInterface apiResponse = retrofit.create(ApiInterface.class);
        SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
        String token = manager.getToken();
        String simInfo = getSimInfo();
        Log.i("token>>>",token);
        Log.i("token>>>",deviceIMEI);
        Log.i("token>>>",deviceName);
        Log.i("token>>>",getDeviceModel());
        Log.i("token>>>",androidVersion);
        Log.i("token>>>",appVersion);
        Log.i("token>>>",simInfo);
        Call<String> call = apiResponse.sendLoginRequestWithQr(token,scannedData, deviceIMEI, deviceName, getDeviceModel(), androidVersion, appVersion, simInfo);
        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(@NonNull Call<String> call, @NonNull Response<String> response) {
                Log.e(TAG, "onResponse: connection" + response.body());
                Log.e(TAG, "SIM LIST:" + simInfo);
                Log.e(TAG, "IMEI " + deviceIMEI + " device name " + deviceName + " android version " + androidVersion + "--- " + appVersion);
                Log.e(TAG, "onResponse: error body" + response.errorBody());
                Log.e(TAG, "onResponse: code " + response.code());
                if (response.isSuccessful()) {
                    try {

                        JSONObject object = null;
                        if (response.body() != null) {
                            object = new JSONObject(response.body());
                        }

                        boolean success = false;
                        if (object != null) {
                            success = object.getBoolean("success");
                        }

                        if (success) {

                            JSONObject userData = object.getJSONObject("data");
                            String accessToken = userData.getString("access_token");
                            String tokenType = userData.getString("token_type");
                            String baseUrl = userData.getString("base_url");

                            //pusher json

                            JSONObject pusherObj = userData.getJSONObject("pusher");
                            String pusherKey = pusherObj.getString("pusher_key");
                            String pusherId = pusherObj.getString("pusher_id");
                            String pusherSecretId = pusherObj.getString("pusher_secret");
                            String pusherClusterId = pusherObj.getString("pusher_cluster");
                            SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
                            manager.setToken(tokenType + " " + accessToken);
                            //pusher data
                            manager.setPusherKey(pusherKey);
                            manager.setPusherId(pusherId);
                            manager.setPusherSecret(pusherSecretId);
                            manager.setPusherCluster(pusherClusterId);

                            //save base url
                            manager.setBaseUrl(UrlContainer.getBaseUrl());

                            Log.e(TAG, "onResponse: " + UrlContainer.getBaseUrl());
                            Log.e(TAG, "pusher key: " + manager.getPusherKey());
                            Log.e(TAG, "pusher id: " + manager.getPusherCluster());
                            Log.e(TAG, "pusher secret: " + manager.getPusherSecret());
                            Log.e(TAG, "pusher cluster: " + manager.getPusherId());
                            connectDevice(false);

                        } else {
                            connectDevice(true);
                            if (object != null) {

                                Toasty.error(MainActivity.this, "Error: " + object.getJSONArray("errors"), Toast.LENGTH_SHORT).show();
                            }
                        }
                    } catch (JSONException e) {
                        connectDevice(true);
                    }
                } else {
                    Toasty.error(MainActivity.this, "Connection failed for " + response.errorBody(), Toast.LENGTH_SHORT).show();
                    connectDevice(true);
                }

            }

            @Override
            public void onFailure(@NonNull Call<String> call, @NonNull Throwable t) {
                Log.e(TAG, "connection failed for : " + t.getMessage());
                Toasty.error(MainActivity.this, "Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                connectDevice(true);
            }
        });

    }

    void gotoLoginActivity() {
        SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
        // Do NOT clear QR data here, so device stays remembered after logout
        Toasty.success(MainActivity.this, "Logout successful", Toast.LENGTH_SHORT, true).show();
        startActivity(new Intent(MainActivity.this, AccountLoginActivity.class));
        finish();
    }

    @SuppressLint("SetTextI18n")
    void connectDevice(boolean isError) {
        SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
        if (isError) {
            manager.setStatus("false");
            gotoLoginActivity();
        }
        setupBackgroundService();
        connectedLL.setBackground(ContextCompat.getDrawable(MainActivity.this, R.drawable.bg_connect));
        statusText.setText("Connected");

    }

    private void restartActivity() {
        Intent intent = new Intent(this, MainActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivity(intent);
        finish();
    }

    String deviceIMEI = "", deviceName, androidVersion, appVersion;

    private void setDeviceInfoData() {
        @SuppressLint("HardwareIds") String id = Settings.Secure.getString(getApplicationContext().getContentResolver(),
                Settings.Secure.ANDROID_ID);
        deviceIMEI = id;
        deviceName = Settings.Global.getString(getContentResolver(), Settings.Global.DEVICE_NAME);
        androidVersion = Build.VERSION.RELEASE;
        try {
            appVersion = getPackageManager().getPackageInfo(getPackageName(), 0).versionName;
        } catch (PackageManager.NameNotFoundException e) {
            e.printStackTrace();
        }

    }

    public String getDeviceModel() {
        String manufacturer = Build.MANUFACTURER;
        String model = Build.MODEL;
        if (model.toLowerCase().startsWith(manufacturer.toLowerCase())) {
            return capitalize(model);
        } else {
            return capitalize(manufacturer) + " " + model;
        }
    }

    private String capitalize(String s) {
        if (s == null || s.isEmpty()) {
            return "";
        }
        char first = s.charAt(0);
        if (Character.isUpperCase(first)) {
            return s;
        } else {
            return Character.toUpperCase(first) + s.substring(1);
        }
    }

    private String getSimInfo() {
        if (ActivityCompat.checkSelfPermission(getApplicationContext(), Manifest.permission.READ_PHONE_STATE) != PackageManager.PERMISSION_GRANTED) {
            return "";
        }

        try {
            String simName = "";
            SubscriptionManager subscriptionManager = SubscriptionManager.from(getApplicationContext());
            if (subscriptionManager == null) {
                return "";
            }

            List<SubscriptionInfo> subscriptionInfos = subscriptionManager.getActiveSubscriptionInfoList();
            if (subscriptionInfos == null || subscriptionInfos.isEmpty()) {
                return "";
            }

            for (int i = 0; i < subscriptionInfos.size(); i++) {
                SubscriptionInfo subInfo = subscriptionInfos.get(i);
                if (subInfo == null) continue;

                if (i == 0) {
                    simName = subInfo.getCarrierName() + "";
                } else if (i == 1) {
                    simName = simName + "," + subInfo.getCarrierName();
                }
            }
            return simName;
        } catch (Exception e) {
            Log.e(TAG, "Error getting SIM info: " + e.getMessage());
            return "";
        }
    }

    void setupBackgroundService() {
        Log.e(TAG, "setupBackgroundService: CALLED");
        
        try {
            SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
            String status = manager.getStatus();
            Log.d(TAG, "User status: " + status);

            if (status.equalsIgnoreCase("true")) {
                Log.d(TAG, "callPusher: Enqueuing SmsWorkManager task");

                try {
                    // Create a OneTimeWorkRequest for SmsWorkManager
                    OneTimeWorkRequest request = new OneTimeWorkRequest.Builder(SmsWorkManager.class)
                            .build();

                    // Enqueue the work request
                    mWorkManager.enqueue(request).getResult()
                            .addListener(() -> Log.d(TAG, "SmsWorkManager work enqueued successfully"),
                                    ContextCompat.getMainExecutor(this));

                    Log.d(TAG, "callPusher: SmsWorkManager work enqueued");

                } catch (Exception e) {
                    // Handle exceptions that might occur during enqueuing
                    Log.e(TAG, "Error enqueuing SmsWorkManager task", e);
                    // Update UI to show warning but don't crash
                    runOnUiThread(() -> {
                        Toast.makeText(MainActivity.this, 
                            "Warning: Background service failed to start", 
                            Toast.LENGTH_SHORT).show();
                    });
                }
            } else {
                Log.d(TAG, "User status is not 'true', skipping background service setup");
            }
        } catch (Exception e) {
            Log.e(TAG, "Critical error in setupBackgroundService", e);
            // Update UI to show error but don't crash the app
            runOnUiThread(() -> {
                try {
                    if (statusText != null) {
                        statusText.setText("Service Error");
                    }
                    Toast.makeText(MainActivity.this, 
                        "Error setting up background services", 
                        Toast.LENGTH_LONG).show();
                } catch (Exception uiError) {
                    Log.e(TAG, "Error updating UI in setupBackgroundService", uiError);
                }
            });
        }
    }

    private void logout() {

        statusText.setText(disconnecting);
        SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
        Retrofit retrofit = RetrofitInstance.getRetrofitInstance(UrlContainer.getBaseUrl());
        ApiInterface apiResponse = retrofit.create(ApiInterface.class);

        String deviceId = manager.getDeviceId();
        String token = manager.getToken();


        try {
            Call<String> call = apiResponse.sendLogOutRequest(token, deviceId);
            call.enqueue(new Callback<String>() {
                @Override
                public void onResponse(@NonNull Call<String> call, @NonNull Response<String> response) {

                    if (response.isSuccessful()) {
                        assert response.body() != null;
                        Log.i(TAG, response.body());
                        logoutAction(false);
                    } else {
                        logoutAction(true);
                    }

                }

                @SuppressLint("CheckResult")
                @Override
                public void onFailure(@NonNull Call<String> call, @NonNull Throwable t) {
                    Toasty.error(MainActivity.this, "Something Went Wrong");
                    logoutAction(true);
                }
            });
        } catch (Exception e) {
            logoutAction(true);
        }


    }

    @SuppressLint("SetTextI18n")
    void logoutAction(boolean isError) {

        SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
        String status = manager.getStatus();

        if (isError) {
            manager.setStatus("false");
            gotoLoginActivity();

        } else {


            manager.setStatus("false");

            connectedLL.setBackground(ContextCompat.getDrawable(MainActivity.this, R.drawable.bg_disconnect));

            statusText.setText("Disconnected");
//            restartActivity();
            disConnectPusher();
            // Always go to login activity after logout
            gotoLoginActivity();
        }

    }

    private void disConnectPusher() {
        // Implement your Pusher disconnection logic here
        SharedPrefManager manager = SharedPrefManager.getInstance(context);
        PusherOdk pusherOdk = PusherOdk.getInstance(UrlContainer.getBaseUrl(), manager.getPusherKey(), manager.getPusherCluster(), manager.getToken());
        pusherOdk.disconnect();
        Log.i(TAG, "Subscription DISCONNECTED");
    }

    private void reConnectPusher() {
        // Implement your Pusher disconnection logic here
        SharedPrefManager manager = SharedPrefManager.getInstance(context);
        PusherOdk pusherOdk = PusherOdk.getInstance(UrlContainer.getBaseUrl(), manager.getPusherKey(), manager.getPusherCluster(), manager.getToken());
        pusherOdk.disconnect();
        Log.i(TAG, "Subscription DISCONNECTED");
    }

    /**
     * Debug method to check SMS capabilities and permissions
     */
    private void checkSmsCapabilities() {
        Log.i(TAG, "=== SMS Capabilities Check ===");

        // Check SMS permissions
        boolean canSendSms = ContextCompat.checkSelfPermission(this, Manifest.permission.SEND_SMS) == PackageManager.PERMISSION_GRANTED;
        boolean canReadSms = ContextCompat.checkSelfPermission(this, Manifest.permission.READ_SMS) == PackageManager.PERMISSION_GRANTED;
        boolean canReceiveSms = ContextCompat.checkSelfPermission(this, Manifest.permission.RECEIVE_SMS) == PackageManager.PERMISSION_GRANTED;
        boolean canReadPhoneState = ContextCompat.checkSelfPermission(this, Manifest.permission.READ_PHONE_STATE) == PackageManager.PERMISSION_GRANTED;

        Log.i(TAG, "SEND_SMS permission: " + canSendSms);
        Log.i(TAG, "READ_SMS permission: " + canReadSms);
        Log.i(TAG, "RECEIVE_SMS permission: " + canReceiveSms);
        Log.i(TAG, "READ_PHONE_STATE permission: " + canReadPhoneState);

        // Check if app is default SMS app
        boolean isDefaultSms = isDefaultSmsApp();
        Log.i(TAG, "Is default SMS app: " + isDefaultSms);

        // Check if device has telephony features
        PackageManager pm = getPackageManager();
        boolean hasTelephony = pm.hasSystemFeature(PackageManager.FEATURE_TELEPHONY);
        Log.i(TAG, "Device has telephony: " + hasTelephony);

        // Check SIM cards and carrier info
        if (canReadPhoneState) {
            try {
                SubscriptionManager subscriptionManager = SubscriptionManager.from(this);
                List<SubscriptionInfo> subscriptionInfoList = subscriptionManager.getActiveSubscriptionInfoList();

                if (subscriptionInfoList != null) {
                    Log.i(TAG, "Number of active SIM cards: " + subscriptionInfoList.size());
                    for (int i = 0; i < subscriptionInfoList.size(); i++) {
                        SubscriptionInfo info = subscriptionInfoList.get(i);
                        Log.i(TAG, "SIM " + (i + 1) + ": " + info.getCarrierName() + " (ID: " + info.getSubscriptionId() + ")");
                        Log.i(TAG, "SIM " + (i + 1) + " Country: " + info.getCountryIso());

                        // Check API level for getMccString/getMncString (requires API 29+)
                        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                            Log.i(TAG, "SIM " + (i + 1) + " MCC/MNC: " + info.getMccString() + "/" + info.getMncString());
                        } else {
                            // For older API levels, we can get MCC/MNC from other sources or skip
                            Log.i(TAG, "SIM " + (i + 1) + " MCC/MNC: Not available (API < 29)");
                        }
                    }
                } else {
                    Log.w(TAG, "No active SIM subscriptions found");
                }
            } catch (Exception e) {
                Log.e(TAG, "Error checking SIM cards: " + e.getMessage());
            }
        }

        // Show warning if not default SMS app
        if (!isDefaultSms && canSendSms) {
            Log.w(TAG, "WARNING: App is not the default SMS app - this may cause carrier blocking");

            // Check if user has already been asked and declined
            SharedPrefManager manager = SharedPrefManager.getInstance(MainActivity.this);
            boolean hasDeclinedDefaultSms = getSharedPreferences("app_preferences", MODE_PRIVATE)
                    .getBoolean("declined_default_sms", false);

            if (!hasDeclinedDefaultSms) {
                new android.app.AlertDialog.Builder(this)
                        .setTitle("SMS App Warning")
                        .setMessage("Your carrier may block SMS sending because this app is not set as the default SMS app. Would you like to set it as default?")
                        .setPositiveButton("Yes", (dialog, which) -> {
                            requestDefaultSmsApp();
                            // Mark that user agreed to set as default
                            getSharedPreferences("app_preferences", MODE_PRIVATE)
                                    .edit()
                                    .putBoolean("agreed_default_sms", true)
                                    .apply();
                        })
                        .setNegativeButton("No", (dialog, which) -> {
                            // Remember that user declined, so we don't ask again
                            getSharedPreferences("app_preferences", MODE_PRIVATE)
                                    .edit()
                                    .putBoolean("declined_default_sms", true)
                                    .apply();
                            dialog.dismiss();
                        })
                        .setNeutralButton("Ask Later", (dialog, which) -> {
                            // Don't save any preference, ask again next time
                            dialog.dismiss();
                        })
                        .show();
            } else {
                Log.i(TAG, "User previously declined default SMS app request");
            }
        }

        Log.i(TAG, "=== End SMS Capabilities Check ===");
    }

    /**
     * Check if the app is set as the default SMS app
     * Some carriers require this for SMS sending to work
     */
    private boolean isDefaultSmsApp() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
            String defaultSmsApp = android.provider.Telephony.Sms.getDefaultSmsPackage(this);
            String currentPackage = getPackageName();
            Log.i(TAG, "Default SMS app: " + defaultSmsApp);
            Log.i(TAG, "Current app: " + currentPackage);
            return currentPackage.equals(defaultSmsApp);
        }
        return true; // For older Android versions, assume it's allowed
    }

    /**
     * Request to become the default SMS app
     */
    private void requestDefaultSmsApp() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.KITKAT) {
            Intent intent = new Intent(android.provider.Telephony.Sms.Intents.ACTION_CHANGE_DEFAULT);
            intent.putExtra(android.provider.Telephony.Sms.Intents.EXTRA_PACKAGE_NAME, getPackageName());
            try {
                startActivity(intent);
            } catch (Exception e) {
                Log.e(TAG, "Error requesting default SMS app: " + e.getMessage());
                Toasty.error(this, "Cannot set as default SMS app", Toast.LENGTH_SHORT).show();
            }
        }
    }

    /**
     * Reset the default SMS app preferences to allow asking the user again
     * Call this if you want to give the user another chance to set as default SMS app
     */
    private void resetSmsAppPreferences() {
        getSharedPreferences("app_preferences", MODE_PRIVATE)
                .edit()
                .remove("declined_default_sms")
                .remove("agreed_default_sms")
                .apply();
        Log.i(TAG, "SMS app preferences have been reset");
        Toasty.info(this, "SMS app preferences reset. You'll be asked again next time.", Toast.LENGTH_SHORT).show();
    }

    /**
     * Check if delivery reports are supported and enabled
     */
    private void checkDeliveryReportSupport() {
        try {
            // Check if the device supports delivery reports
            TelephonyManager telephonyManager = (TelephonyManager) getSystemService(Context.TELEPHONY_SERVICE);

            Log.i(TAG, "=== DELIVERY REPORT DIAGNOSTICS ===");
            Log.i(TAG, "Network operator: " + telephonyManager.getNetworkOperatorName());
            Log.i(TAG, "SIM operator: " + telephonyManager.getSimOperatorName());
            Log.i(TAG, "Is default SMS app: " + isDefaultSmsApp());

            // Note: There's no direct API to check if delivery reports are supported
            // This varies by carrier and device
            Log.i(TAG, "Note: Delivery report support depends on:");
            Log.i(TAG, "1. Carrier support for delivery receipts");
            Log.i(TAG, "2. Recipient device settings");
            Log.i(TAG, "3. Network conditions");
            Log.i(TAG, "4. Being set as default SMS app (recommended)");
            Log.i(TAG, "=====================================");

        } catch (Exception e) {
            Log.e(TAG, "Error checking delivery report support: " + e.getMessage());
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.main_menu, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();

        if (id == R.id.menu_reset_sms_preferences) {
            resetSmsAppPreferences();
            return true;
        } else if (id == R.id.menu_check_sms_capabilities) {
            checkSmsCapabilities();
            return true;
        } else if (id == R.id.menu_check_delivery_reports) {
            checkDeliveryReportSupport();
            return true;
        }

        return super.onOptionsItemSelected(item);
    }

}
