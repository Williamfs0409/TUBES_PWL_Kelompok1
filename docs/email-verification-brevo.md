# CityZen Email Verification via Brevo

CityZen uses Laravel SMTP mail for email verification. For a free provider that can send to real inboxes, use Brevo SMTP Relay.

## Railway Variables

Set these variables in the Railway service:

```env
APP_URL=https://cityzen-usu.up.railway.app
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_brevo_login_email
MAIL_PASSWORD=your_brevo_smtp_key
MAIL_FROM_ADDRESS=your_verified_sender_email
MAIL_FROM_NAME=CityZen
```

## Brevo Setup

1. Create a Brevo account.
2. Open Transactional Email and activate SMTP.
3. Create or copy your SMTP key.
4. Verify a sender email or domain in Brevo.
5. Put the SMTP values into Railway Variables.
6. Redeploy CityZen.

After this, new CityZen registrations receive a real email verification link.
