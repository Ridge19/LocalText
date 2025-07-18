package dev.vlab.tweetsms.service;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.util.Log;

import androidx.annotation.NonNull;

import dev.vlab.tweetsms.apiclient.ApiInterface;
import dev.vlab.tweetsms.apiclient.RetrofitInstance;
import dev.vlab.tweetsms.helper.SharedPrefManager;

import dev.vlab.tweetsms.helper.UrlContainer;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;

class DeliveredReceiver extends BroadcastReceiver {

    String TAG = "TAG";

    @Override
    public void onReceive(Context context, Intent intent) {

        long smsId = intent.getLongExtra("SMS_ID", 0);
        Log.i(TAG, "DeliveredReceiver triggered for SMS ID: " + smsId);
        Log.i(TAG, "Intent action: " + intent.getAction());
        Log.i(TAG, "Result code: " + getResultCode());

        // Check if we actually received a delivery confirmation
        if (smsId > 0) {
            Log.i(TAG, "Processing delivery confirmation for SMS " + smsId);
            changeStatus(String.valueOf(smsId), "Delivered", context);
        } else {
            Log.e(TAG, "Invalid SMS ID received in delivery confirmation: " + smsId);
        }

    }

    void changeStatus(String messageId, String status, Context context) {

        Log.e(TAG, "=== DELIVERY STATUS UPDATE ===");
        Log.e(TAG, "Message ID: " + messageId);
        Log.e(TAG, "Status: " + status);
        Log.e(TAG, "Timestamp: " + System.currentTimeMillis());
        Log.e(TAG, "=============================");

        SharedPrefManager manager = SharedPrefManager.getInstance(context);
        Retrofit retrofit = RetrofitInstance.getRetrofitInstance(UrlContainer.getBaseUrl());
        ApiInterface apiResponse = retrofit.create(ApiInterface.class);


        String mainStatus = status.equalsIgnoreCase("sent") ? "4" : status.equalsIgnoreCase("delivered") ? "1" : "9";
        String token = manager.getToken();

        Log.i(TAG, "Updating message " + messageId + " to status code: " + mainStatus + " (1=delivered, 4=sent, 9=fail)");

        Call<String> call = apiResponse.updateMessageStatus(token, messageId, mainStatus, ""); //1=delivered  2=pending 3=schedule 4=sent 9=fail

        call.enqueue(new Callback<String>() {
            @Override
            public void onResponse(@NonNull Call<String> call, @NonNull Response<String> response) {
                Log.e(TAG, "server response : " + response.body());
            }

            @Override
            public void onFailure(@NonNull Call<String> call, @NonNull Throwable t) {
                Log.e(TAG, "onFailure: " + t.getMessage());
            }
        });


    }
}


