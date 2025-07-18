# Local Text Android App

Local Text is an Android application developed for @businesslocal that enables businesses to send and manage SMS notifications using their own Android devices as SMS gateways. The app is designed for seamless integration with the Local Text web platform, allowing users to connect their devices via QR code and manage SMS campaigns, contacts, and templates from a central dashboard.

## Features

- **Device Pairing via QR Code:**
  - Securely connect your Android device to your Local Text account by scanning a QR code from the web dashboard.
- **SMS Sending & Receiving:**
  - Send single or bulk SMS messages directly from your device.
  - Receive and upload incoming SMS to the platform.
- **Campaign & Contact Management:**
  - Manage SMS campaigns, contacts, groups, and templates from the web dashboard.
- **Device Management:**
  - View, add, or remove connected devices. Enforce device limits per user.
- **Authentication & Security:**
  - Login with username/email and password.
  - OTP-based password recovery.
  - "Remember Me" option for persistent device pairing.
- **Session Management:**
  - Logout and session handling with automatic redirection to the login screen.
  - Option to forget device when "Remember Me" is unticked.
- **SIM Slot Support:**
  - Detect and use multiple SIM slots for sending SMS.

## How It Works

1. **Download & Install:**
   - Download the Local Text APK from the web dashboard and install it on your Android device.
2. **Pair Device:**
   - Log in to the web dashboard, go to "Add Device", and scan the QR code using the app.
3. **Send SMS:**
   - Use the web dashboard to send SMS via your paired device(s).
4. **Manage Devices:**
   - Add, remove, or monitor devices from the dashboard. Device limits are enforced for each user.
5. **Authentication:**
   - Use the "Remember Me" option to keep your device paired, or untick to remove the connection on logout.

## Screenshots

### Desktop
<p align="center"> 
  <figure>
    <img src="app/src/main/res/drawable/Screenshots/device_pairing.png" alt="Device Pairing" width="600"/>
    <figcaption>Device Pairing: Scan the QR code to connect your device.</figcaption>
  </figure>
  <figure>
    <img src="app/src/main/res/drawable/Screenshots/login_screen.png" alt="Login Screen" width="600"/>
    <figcaption>Login Screen: Enter your credentials to access the app.</figcaption>
  </figure>
</p>

### Android
<p align="center">
  <figure>
    <img src="app/src/main/res/drawable/Screenshots/android_login.png" alt="Android Login" width="300"/>
    <figcaption>Android Login: Enter your credentials and optionally use Remember Me to keep your details and device paired.</figcaption>
  </figure>
  <figure>
    <img src="app/src/main/res/drawable/Screenshots/android_device_pairing.png" alt="Android Device Pairing" width="300"/>
    <figcaption>Device Pairing: Scan the QR code from the web dashboard to connect your device.</figcaption>
  </figure>
  <figure>
    <img src="app/src/main/res/drawable/Screenshots/android_connected.png" alt="Connected to System" width="300"/>
    <figcaption>Connected: Your device is now paired and ready to send SMS.</figcaption>
  </figure>
</p>

## Tech Stack
- Java (Android 15)
- Retrofit2 (API calls)
- ZXing (QR code scanning)
- Toasty (User notifications)
- Material Design UI

## Contributors
- [@Ridge19](https://github.com/Ridge19) (Developer)
- [@businesslocal](https://github.com/businesslocal) (Organisation)

## License
This project is proprietary and developed for @businesslocal. For licensing or business inquiries, please contact the organisation directly.

---

For more information, visit [localtext.businesslocal.com.au](https://localtext.businesslocal.com.au) or contact [@Ridge19](https://github.com/Ridge19).
