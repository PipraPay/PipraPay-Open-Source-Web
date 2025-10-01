# [PipraPay](https://piprapay.com) - Payment Automation Platform

PipraPay is a self-hosted payment automation platform by QubePlug Bangladesh.
Host it on your own server, define your workflow, install plugins and themes, and stay in control. It’s free to use with no monthly fees. Visit https://piprapay.com/ for more info.

---

## Table of Contents

- Documentation
- Features
- Installation
- License & Brand Protection
- Trademark Policy
- Contributing
- Community & Support

---

## 📖 Documentation

- Developer guides live in `docs/`:
  - Payment Gateway Plugins: [docs/Payment-Gateway-Plugins-Developer-Guide.md](docs/Payment-Gateway-Plugins-Developer-Guide.md)
  - Module Plugins: [docs/PipraPay-Module-Plugin-Developer-Guide.md](docs/PipraPay-Module-Plugin-Developer-Guide.md)

- PipraPay Plugins & Integrations Directory
    - Plugin Directory: [docs/Plugin-Directory.md](docs/Plugin-Directory.md)
- Browse all docs: [docs/](docs/)
- API docs: [piprapay.readme.io](https://piprapay.readme.io)
- Explore PipraPay Demo: [https://demo.piprapay.com/admin/login](https://demo.piprapay.com/admin/login)
   - username: `demo`
   - password: `123456`

---

## 🚀 Features

- Self-hosted: deploy on your own server with no third‑party lock‑in.
- Plugin-based architecture for gateways, modules, and tools.
- Theme support for admin dashboard branding.
- Multiple gateways (Stripe, bKash, Nagad, etc.) via plugins.
- Automatic transaction verification to reduce manual work.
- Scales to unlimited transactions (within your server capacity).
- Integrations with platforms like WooCommerce, WHMCS, and WordPress.

---

## ⚙️ Installation

1. Clone this repository:
   ```bash
   git clone https://github.com/PipraPay/PipraPay-Open-Source-Web.git
   ```
2. Prepare your environment: PHP, a database, and a web server (Apache/Nginx).
3. Upload or place the source files on your server.
4. Install dependencies (if any) as per your setup.
5. Configure database credentials, domain, and environment settings.
6. Access the admin panel, install plugins, and configure gateways.

---

## 📚 License & Brand Protection

- Code is licensed under the GNU Affero General Public License v3.0 (AGPL‑3.0). See [LICENSE](LICENSE).

---

## 🛡️ Trademark Policy

The PipraPay name, logo, and brand identity are trademarks of QubePlug Bangladesh.

### ✅ What you can do
- Use PipraPay as provided, including branding.
- Build and share plugins, modules, and themes.
- Contribute back to this repository.

### ❌ What you cannot do
- Rebrand the system (e.g., rename it to another product).
- Remove or replace the PipraPay name/logo/brand in a modified version and present it as your own brand.

---

## 🤝 Contributing

We welcome contributions from the community. To contribute:

1. Fork this repository.
2. Create a new branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. Make your changes.
4. Commit with a clear message.
5. Push to your fork and open a Pull Request describing your changes.

---

## ❓ Common Issues & Solutions

<details>
<summary>⏳ Pending payment is not auto-verifying</summary>

**Possible Cause:**  
The cron job responsible for verifying payments is not running.  

**Solution:**  
1. Log in to the PipraPay **Admin Panel**.  
2. Navigate to **System Settings > Cron Job**.  
3. Copy the provided command.  
4. Set the cron job in your hosting control panel to run every **10 minutes**.  

</details>

---

<details>
<summary>🔌 How to download PipraPay tool app</summary>

**Solution:**  
1. Downlolad from **PipraPay Tool** App from here. [Download Tool App](https://github.com/PipraPay/PipraPay-Open-Source-Web/blob/main/docs/apps/piprapay-tool.apk).  

</details>

---

<details>
<summary>🔌 PipraPay tool app is not connecting</summary>

**Possible Cause:**  
The webhook URL or app permission or app service is not properly configured.  

**Solution:**  
1. First, add your **full webhook URL** (e.g., `https://example.com/?webhook=**********` or `https://pay.example.com/?webhook=**********`).  
2. Allow all permission from **PipraPay Tool** app info.  
2. Allow background running.  
2. Force stop the **PipraPay Tool** App after setup **webhook URL**, **App Permission**. Open **PipraPay Tool** App then check in admin panel.  

</details>

---

<details>
<summary>💳 No payment methods showing in the Admin Panel</summary>

**Possible Cause:**  
The payment method plugins are not activated.  

**Solution:**  
1. Go to **Admin Panel > Plugin > Installed Plugins**.  
2. Activate the required **payment method plugins**.  

</details>

---

<details>
<summary>🛒 Payment methods not showing on the checkout page</summary>

**Possible Causes:**  
- Minimum and maximum payment amounts are not configured.  
- The payment method is disabled.  

**Solution:**  
1. Set the **minimum and maximum amount** for the payment method.  
2. Verify the **status**:  
   - If it is **disabled**, switch it to **enabled**.  

</details>

---

<details>
<summary>💱 Currency mismatch (e.g., 1000 BDT showing as $1000 USD)</summary>

**Possible Cause:**  
Currency exchange rates are not set correctly.  

**Solution:**  
1. In the **Admin Panel**, go to **System Settings > Currency Settings**.  
2. Update the currency rate.  
   - Example: `1 BDT = 0.0082 USD` (not `1 BDT = 1 USD`).  

</details>

---

<details>
<summary>🔄 Website does not redirect after successful payment</summary>

**Possible Cause:**  
Auto-redirect option is not enabled.  

**Solution:**  
1. Go to **Admin Panel > Appearance > Customize**.  
2. Enable the **Auto Redirect** option.  

</details>

---

<details>
<summary>🔑 Forgot Admin Panel password</summary>

**Solution:**  
1. Log in to your **hosting control panel**.  
2. Open the directory where **PipraPay files** are located.  
3. Edit the file `pp_config.php`.  
4. Change:  
   ```php
   $password_reset = 'off';
   ```  
   to  
   ```php
   $password_reset = 'on';
   ```  
5. Go to the **PipraPay Admin Login page** and click **Reset Password**.  
6. Set your new password.  
7. Re-edit `pp_config.php` and set:  
   ```php
   $password_reset = 'off';
   ```  

⚠️ **Important:** Always revert `$password_reset` to `'off'` after resetting for security reasons.  

</details>

---

<details>
<summary>🌐 Payment API returning errors</summary>

**Possible Cause:**  
Incorrect API credentials or endpoint configuration.  

**Solution:**  
1. Verify your **API key** and **secret** from the Admin Panel.  
2. Ensure the API endpoint matches your environment (**sandbox** vs **production**).  
3. Test using a REST client (e.g., Postman).  

</details>

---

<details>
<summary>📦 Checkout page not loading properly</summary>

**Possible Cause:**  
JavaScript conflicts or missing plugin files.  

**Solution:**  
1. Clear your **browser cache**.  
2. Recheck plugin installation in the Admin Panel.  
3. Disable conflicting plugins or themes temporarily.  

</details>

---

<details>
<summary>🖥️ Hosting errors (500 Internal Server Error)</summary>

**Possible Cause:**  
Server misconfiguration or insufficient resources.  

**Solution:**  
1. Check your server **error logs**.  
2. Increase **memory limit** and **max execution time** in PHP settings.  
3. Restart your web server (Apache/Nginx).  

</details>

---

<details>
<summary>🔐 SSL certificate issues</summary>

**Possible Cause:**  
Expired or misconfigured SSL certificate.  

**Solution:**  
1. Renew your SSL certificate with your hosting provider.  
2. Update the certificate path in your server configuration.  
3. Test the SSL status using online tools like **SSL Labs**.  

</details>

---

<details>
<summary>📧 Customers not receiving email notifications</summary>

**Possible Cause:**  
Email server or SMTP configuration is incorrect.  

**Solution:**  
1. Go to **Admin Panel > Plugins > Installed Plugin > SMTP Mailer Pro**.  
2. Go to **Admin Panel > Modules > SMTP Mailer Pro > Configure SMTP with valid credentials**.   
3. Test the email function using the built-in test tool.  

</details>

---

<details>
<summary>⚙️ Database connection failed</summary>

**Possible Cause:**  
Invalid database credentials or server downtime.  

**Solution:**  
1. Verify database credentials in `pp_config.php`.  
2. Check database server status.  
3. Ensure correct **host, port, username, and password** are set.  

</details>

---

## 🤷‍♂️ Community & Support

- Website: https://piprapay.com/
- Documentation: [docs/](docs/)
- License: [LICENSE](LICENSE)
- Issues & bug reports: https://github.com/PipraPay/PipraPay-Open-Source-Web/issues

