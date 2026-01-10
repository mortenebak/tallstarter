# Social Login Setup Guide

This starter kit includes support for social authentication using Laravel Socialite. You can enable users to sign in or register using their Google, Facebook, or Twitter/X accounts.

## Features

- **OAuth 2.0 support** for Google and Facebook
- **OAuth 1.0a support** for Twitter/X
- **Automatic account creation** for new social login users
- **Email verification** automatically set for social login users
- **Random password generation** for social login accounts (users never need to know this password)
- **Seamless integration** with existing authentication flow

## Overview

Social login allows users to authenticate using their existing accounts from popular providers without creating a new password. When a user signs in via social login:

1. They are redirected to the provider's authentication page
2. After authorization, they're redirected back to your application
3. If it's their first login, a new user account is created
4. If they already have an account, they're logged in
5. The user is redirected to the dashboard

## Provider Setup

### Google

#### 1. Create a Google Cloud Project

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Click **Select a project** > **New Project**
3. Enter a project name (e.g., "My Laravel App")
4. Click **Create**

#### 2. Enable Google+ API

1. In your project, go to **APIs & Services** > **Library**
2. Search for "Google+ API" or "Google Identity Services"
3. Click on it and click **Enable**

**Note:** Google has deprecated Google+ API. You should use Google Identity Services (OAuth 2.0). The Socialite package handles this automatically.

#### 3. Create OAuth 2.0 Credentials

1. Go to **APIs & Services** > **Credentials**
2. Click **Create Credentials** > **OAuth client ID**
3. If prompted, configure the OAuth consent screen:
   - Choose **External** (unless you have a Google Workspace)
   - Fill in the required fields (App name, User support email, Developer contact)
   - Click **Save and Continue**
   - Skip scopes (click **Save and Continue**)
   - Add test users if your app is in testing (optional)
   - Click **Save and Continue**
   - Review and click **Back to Dashboard**
4. Back at credentials, click **Create Credentials** > **OAuth client ID**
5. Select **Web application**
6. Set the name (e.g., "Laravel Social Login")
7. Add **Authorized redirect URIs**:
   ```
   http://laravel-livewire-starter-kit.test/auth/google/callback
   https://yourdomain.com/auth/google/callback
   ```
   Replace `yourdomain.com` with your actual domain.
8. Click **Create**
9. Copy the **Client ID** and **Client Secret**

#### 4. Configure Laravel

Add to your `.env` file:

```env
GOOGLE_CLIENT_ID=your-client-id-here
GOOGLE_CLIENT_SECRET=your-client-secret-here
GOOGLE_REDIRECT_URI=http://laravel-livewire-starter-kit.test/auth/google/callback
```

For production, update the redirect URI:

```env
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

---

### Facebook

#### 1. Create a Facebook App

1. Go to [Facebook Developers](https://developers.facebook.com/)
2. Click **My Apps** > **Create App**
3. Select **Consumer** as the app type
4. Fill in the required information:
   - **App Name**: Your app name
   - **App Contact Email**: Your email
   - **Business Account**: Optional
5. Click **Create App**

#### 2. Add Facebook Login Product

1. In your app dashboard, find **Add Products to Your App**
2. Click **Set Up** on **Facebook Login**
3. Choose **Web** as your platform
4. Enter your site URL: `http://laravel-livewire-starter-kit.test` (or your domain)
5. Click **Save**

#### 3. Configure Facebook Login Settings

1. Go to **Facebook Login** > **Settings**
2. Add **Valid OAuth Redirect URIs**:
   ```
   http://laravel-livewire-starter-kit.test/auth/facebook/callback
   https://yourdomain.com/auth/facebook/callback
   ```
3. Click **Save Changes**

#### 4. Get Your App ID and Secret

1. Go to **Settings** > **Basic**
2. Copy your **App ID** (Client ID)
3. Copy your **App Secret** (Client Secret) - you may need to click **Show** and enter your password

#### 5. Configure Permissions (Optional)

1. Go to **App Review** > **Permissions and Features**
2. Request access to `email` permission (required for getting user email)
3. Facebook may require app review for production use

#### 6. Configure Laravel

Add to your `.env` file:

```env
FACEBOOK_CLIENT_ID=your-app-id-here
FACEBOOK_CLIENT_SECRET=your-app-secret-here
FACEBOOK_REDIRECT_URI=http://laravel-livewire-starter-kit.test/auth/facebook/callback
```

For production:

```env
FACEBOOK_REDIRECT_URI=https://yourdomain.com/auth/facebook/callback
```

---

### Twitter/X

**Important:** Twitter/X uses OAuth 1.0a, which requires different configuration keys than OAuth 2.0 providers.

#### 1. Create a Twitter Developer Account

1. Go to [Twitter Developer Portal](https://developer.twitter.com/en/portal/dashboard)
2. Sign in with your Twitter/X account
3. Apply for a developer account if you haven't already
4. Complete the application form (purpose, use case, etc.)
5. Wait for approval (usually takes a few hours to a few days)

#### 2. Create a New App

1. Once approved, go to the [Developer Portal Dashboard](https://developer.twitter.com/en/portal/dashboard)
2. Click **+ Create Project** or **+ Create App**
3. Fill in the required information:
   - **App name**: Your app name
   - **App environment**: Select appropriate environment
4. Click **Next** and complete the setup

#### 3. Configure App Settings

1. Go to your app's **Settings** tab
2. Under **User authentication settings**, click **Set up**
3. Enable **OAuth 1.0a**
4. Set **App permissions**: At minimum, select **Read** (you may need **Read and write** depending on your needs)
5. Set **Type of App**: Web App
6. Set **App website URL**: `http://laravel-livewire-starter-kit.test` (or your domain)
7. Add **Callback URI / Redirect URL**:
   ```
   http://laravel-livewire-starter-kit.test/auth/twitter/callback
   https://yourdomain.com/auth/twitter/callback
   ```
8. Set **Website URL**: Your website URL
9. Click **Save**

#### 4. Get Your API Keys

1. Go to your app's **Keys and tokens** tab
2. Under **Consumer Keys**, you'll find:
   - **API Key** (this is your Client ID / Identifier)
   - **API Secret Key** (this is your Client Secret)
3. Copy both values
4. **Important:** Keep these secret and never commit them to version control

#### 5. Configure Laravel

Add to your `.env` file:

```env
TWITTER_CLIENT_ID=your-api-key-here
TWITTER_CLIENT_SECRET=your-api-secret-key-here
TWITTER_REDIRECT_URI=http://laravel-livewire-starter-kit.test/auth/twitter/callback
```

For production:

```env
TWITTER_REDIRECT_URI=https://yourdomain.com/auth/twitter/callback
```

**Note:** Even though Twitter/X uses OAuth 1.0a internally, Laravel Socialite expects `client_id` and `client_secret` in the configuration. The Socialite package automatically maps these to `identifier` and `secret` for the OAuth 1.0a client internally. This is already configured correctly in the starter kit.

---

## Laravel Configuration

### Services Configuration

The `config/services.php` file is already configured for all three providers:

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],

'facebook' => [
    'client_id' => env('FACEBOOK_CLIENT_ID'),
    'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
    'redirect' => env('FACEBOOK_REDIRECT_URI'),
],

'twitter' => [
    'client_id' => env('TWITTER_CLIENT_ID'),
    'client_secret' => env('TWITTER_CLIENT_SECRET'),
    'redirect' => env('TWITTER_REDIRECT_URI'),
],
```

**Note:** Even though Twitter/X uses OAuth 1.0a internally, the configuration uses `client_id` and `client_secret` (same as OAuth 2.0 providers). Laravel Socialite automatically handles the mapping to OAuth 1.0a's `identifier` and `secret` internally.

### Routes

Social login routes are already configured in `routes/auth.php`:

- `GET /auth/{provider}/redirect` - Redirects to provider's login page
- `GET /auth/{provider}/callback` - Handles the callback from provider

Supported providers: `google`, `facebook`, `twitter`

### Environment Variables

After setting up each provider, add the credentials to your `.env` file:

```env
# Google
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://laravel-livewire-starter-kit.test/auth/google/callback

# Facebook
FACEBOOK_CLIENT_ID=your-facebook-app-id
FACEBOOK_CLIENT_SECRET=your-facebook-app-secret
FACEBOOK_REDIRECT_URI=http://laravel-livewire-starter-kit.test/auth/facebook/callback

# Twitter/X
TWITTER_CLIENT_ID=your-twitter-api-key
TWITTER_CLIENT_SECRET=your-twitter-api-secret
TWITTER_REDIRECT_URI=http://laravel-livewire-starter-kit.test/auth/twitter/callback
```

### Clear Configuration Cache

After updating your `.env` file, clear the configuration cache:

```bash
php artisan config:clear
```

Or if you're using config caching in production:

```bash
php artisan config:cache
```

### Conditional Display

The social login buttons on the login and register pages are automatically shown or hidden based on your configuration:

- **Buttons only appear if credentials are configured**: Each provider's button (Google, Facebook, Twitter/X) will only be displayed if both `client_id` and `client_secret` are set in your configuration
- **"Or continue with" divider is conditional**: The divider line and text only appear if at least one social login provider is configured
- **Dynamic grid layout**: The button grid automatically adjusts its layout based on how many providers are configured:
  - 1 provider → Single column layout
  - 2 providers → Two column layout
  - 3 providers → Three column layout

This means:
- If you haven't configured any social login providers, the entire social login section is hidden
- If you only configure Google, only the Google button appears
- You can configure just the providers you want to use without showing broken buttons for unconfigured ones

**Example:** If you only want to use Google login, simply add only the Google credentials to your `.env` file, and only the Google button will be displayed on the login and register pages.

---

## How It Works

### User Flow

1. User clicks on a social login button (Google, Facebook, or Twitter/X)
2. User is redirected to the provider's authentication page
3. User authorizes your application
4. Provider redirects back to your callback URL with an authorization code/token
5. Your application exchanges the code for user information
6. System checks if a user with that email already exists
   - If new: Creates a new user account with a random password
   - If existing: Updates provider information and logs them in
7. User is logged in and redirected to the dashboard

### Database Schema

The following columns are added to the `users` table for social login:

- `provider_name` (string, nullable) - The provider used (google, facebook, twitter)
- `provider_id` (string, nullable) - The user's ID from the provider
- `provider_token` (text, nullable) - The OAuth token (encrypted/hashed in production)

These fields are added via migration: `2026_01_10_192928_add_social_login_fields_to_users_table.php`

### Password Handling

For social login users:
- A random 32-character password is automatically generated
- The password is automatically hashed using Laravel's password hashing
- Users never need to know or use this password
- They will always log in via their social provider

For existing users who later use social login:
- Their existing password is preserved
- They can still log in with email/password if needed
- Social login is added as an additional authentication method

---

## Testing

### Local Development

1. Make sure all environment variables are set in your `.env` file
2. Use your local domain for redirect URIs (e.g., `http://laravel-livewire-starter-kit.test`)
3. Clear config cache: `php artisan config:clear`
4. Visit `/register` or `/login`
5. Click on a social login button
6. Complete the OAuth flow

### Running Tests

The starter kit includes tests for social login functionality:

```bash
php artisan test --filter=SocialiteTest
```

Or run all tests:

```bash
php artisan test
```

---

## Production Deployment

### Important Considerations

1. **Update Redirect URIs**: Make sure all redirect URIs in provider dashboards point to your production domain
2. **Environment Variables**: Set all social login credentials in your production `.env` file
3. **HTTPS Required**: Most providers require HTTPS in production. Ensure your production site uses SSL
4. **Domain Verification**: Some providers (especially Facebook) may require domain verification
5. **App Review**: Facebook requires app review for certain permissions and production use

### Production Redirect URIs

Update your `.env` file with production URLs:

```env
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
FACEBOOK_REDIRECT_URI=https://yourdomain.com/auth/facebook/callback
TWITTER_REDIRECT_URI=https://yourdomain.com/auth/twitter/callback
```

Also update these in each provider's dashboard settings.

### Security Best Practices

1. **Never commit credentials**: Keep all client IDs and secrets in `.env` file, never in version control
2. **Use environment-specific apps**: Consider creating separate apps for development and production
3. **Rotate secrets regularly**: Periodically rotate your API keys and secrets
4. **Monitor usage**: Check provider dashboards regularly for unusual activity
5. **Limit permissions**: Only request the minimum permissions needed from providers

---

## Troubleshooting

### Common Issues

#### "Missing client credentials key [identifier]" (Twitter)

**Solution:** Make sure `TWITTER_CLIENT_ID` and `TWITTER_CLIENT_SECRET` are set in your `.env` file and that you've cleared the config cache.

#### "Invalid redirect_uri" or "redirect_uri_mismatch"

**Solution:** 
- Verify the redirect URI in your `.env` matches exactly what's configured in the provider's dashboard
- Make sure there are no trailing slashes or extra characters
- For production, ensure you're using HTTPS

#### "Email not provided" (Twitter/X)

**Issue:** Twitter/X OAuth 1.0a may not always provide email addresses, especially if the user hasn't verified their email with Twitter.

**Solution:** The application handles this by using the user's Twitter handle/name. You may want to add additional logic to handle users without emails (e.g., prompt them to add an email after registration).

#### "App Not Setup: This app is still in development mode" (Facebook)

**Solution:**
- Add test users in Facebook App Settings > Roles > Test Users
- Or submit your app for review to enable public access

#### "Access Denied" or "User Denied Permission"

**Solution:** 
- User clicked "Cancel" on the provider's authorization page
- Check that your app has requested the correct permissions
- For Facebook, ensure email permission is approved in App Review

### Debugging

1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Check provider dashboards**: Look for error logs or analytics
3. **Test with curl**: Test OAuth flow manually if needed
4. **Verify environment variables**: Use `php artisan tinker` to check config values:
   ```php
   config('services.google.client_id')
   ```

---

## Key Files

### Controllers

- `app/Http/Controllers/Auth/SocialLoginController.php` - Handles OAuth redirect and callback

### Routes

- `routes/auth.php` - Social login routes

### Configuration

- `config/services.php` - Provider configuration

### Migrations

- `database/migrations/2026_01_10_192928_add_social_login_fields_to_users_table.php` - Adds social login fields

### Views

- `resources/views/livewire/auth/login.blade.php` - Login page with social buttons
- `resources/views/livewire/auth/register.blade.php` - Register page with social buttons

### Tests

- `tests/Feature/Auth/SocialiteTest.php` - Social login tests

---

## Additional Resources

- [Laravel Socialite Documentation](https://laravel.com/docs/socialite)
- [Google OAuth 2.0 Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Facebook Login Documentation](https://developers.facebook.com/docs/facebook-login/)
- [Twitter OAuth 1.0a Documentation](https://developer.twitter.com/en/docs/authentication/oauth-1-0a)

---

## FAQ

**Q: Can users link multiple social accounts to one email?**

A: Yes, if they use the same email address across providers, the system will associate all providers with the same user account.

**Q: What happens if a user signs up with email/password and later uses social login with the same email?**

A: The existing account will be updated with the provider information, and the user's password will be preserved. They can use either authentication method.

**Q: Can I disable social login for specific providers?**

A: Yes, simply don't add the credentials for that provider, or remove the social login buttons from the login/register views.

**Q: Do I need to handle email verification for social login users?**

A: The application automatically sets `email_verified_at` for social login users since the email is verified by the provider.

**Q: What if Twitter/X doesn't provide an email address?**

A: The application handles this gracefully. The user will be created with their Twitter handle/name. You may want to prompt them to add an email address after registration for account recovery purposes.
