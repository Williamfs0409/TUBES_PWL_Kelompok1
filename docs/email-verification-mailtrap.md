# CityZen Email Verification via Mailtrap

CityZen uses Laravel SMTP mail for email verification. The application code works with Mailtrap because it only needs SMTP variables.

## Option A: Mailtrap Sandbox

Use this for demo and development. Emails appear inside the Mailtrap inbox, not in Gmail/Yahoo.

Set these Railway variables:

```env
APP_URL=https://cityzen-usu.up.railway.app
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_mailtrap_sandbox_username
MAIL_PASSWORD=your_mailtrap_sandbox_password
MAIL_FROM_ADDRESS=noreply@cityzen.test
MAIL_FROM_NAME=CityZen
```

## Option B: Mailtrap Email Sending

Use this when verification emails must reach real inboxes. Mailtrap may require sender/domain verification before production delivery.

Set these Railway variables using the SMTP credentials shown in your Mailtrap Email Sending dashboard:

```env
APP_URL=https://cityzen-usu.up.railway.app
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_mailtrap_smtp_username
MAIL_PASSWORD=your_mailtrap_smtp_password
MAIL_FROM_ADDRESS=your_verified_sender_email
MAIL_FROM_NAME=CityZen
```

## Mailtrap Setup

1. Create or open a Mailtrap account.
2. For testing, open **Email Testing** and copy the SMTP credentials from your inbox integration settings.
3. For real email delivery, open **Email Sending**, verify your sender/domain, then copy the SMTP credentials.
4. Put the variables into Railway.
5. Redeploy CityZen.
6. Register a new account and open the verification link from Mailtrap or the real recipient inbox.
