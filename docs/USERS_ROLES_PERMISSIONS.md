# Users, Roles & Permissions

This starter kit includes a complete admin system for managing users, roles, and permissions, built on top of [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission). It ships with a separate admin dashboard, a seeded permission set, a Super Admin role, and user impersonation.

## Features

- **Admin dashboard** - A separate backend area at `/admin`, protected by permissions
- **User management** - Create, view, edit, and delete users; assign roles
- **Role management** - Create, edit, and delete roles; assign permissions to roles
- **Permission management** - Create, edit, and delete permissions
- **Seeded defaults** - A sensible default permission set and a Super Admin role out of the box
- **Super Admin creation command** - Create your first admin user from the CLI
- **Impersonation** - Log in as any other user and switch back, gated by a permission
- **Search & pagination** - All admin list views support searching and per-page settings

## Seeded Permissions

`database/seeders/PermissionSeeder.php` seeds the following permissions:

| Permission | Grants |
|------------|--------|
| `access dashboard` | Access to the admin dashboard at `/admin` |
| `impersonate` | Ability to impersonate other users |
| `view users` | View the users list and individual users |
| `create users` | Create new users |
| `update users` | Edit existing users |
| `delete users` | Delete users |
| `view roles` | View the roles list |
| `create roles` | Create new roles |
| `update roles` | Edit existing roles |
| `delete roles` | Delete roles |
| `view permissions` | View the permissions list |
| `create permissions` | Create new permissions |
| `update permissions` | Edit existing permissions |
| `delete permissions` | Delete permissions |

Run the seeders with:

```bash
php artisan db:seed
```

`DatabaseSeeder` calls `PermissionSeeder` and `RoleSeeder` in order. Both seeders use `updateOrCreate`, so they are safe to re-run.

## The Super Admin Role

`database/seeders/RoleSeeder.php` creates a role named `Super Admin` and assigns it **all** seeded permissions. Note that this is a plain role with every permission attached - there is no implicit `Gate::before` bypass, so newly created permissions are not automatically granted to the role. Re-run the seeders (or edit the role in `/admin/roles`) to grant new permissions to the Super Admin role.

## Creating the First Super Admin User

Use the included Artisan command:

```bash
php artisan app:create-super-admin
```

The command (`app/Console/Commands/CreateSuperAdminCommand.php`) will interactively:

1. Ask whether you want to create a super admin user
2. Ask for the user's name, email, and password
3. Ask whether the user should be a super admin
4. Create the user (with locale `en`) and, if confirmed, assign the `Super Admin` role

This command is also run automatically at the end of `composer create-project` (see the `post-create-project-cmd` script in `composer.json`). Make sure you have run `php artisan db:seed` first, so the `Super Admin` role exists.

## Admin Dashboard

The admin area lives under `/admin` and requires authentication. Each section is protected by `can:` middleware on the route **and** an `authorize()` call in the Livewire component:

| Route | Component | Required permission |
|-------|-----------|---------------------|
| `GET /admin` | `Admin\Index` | `access dashboard` |
| `GET /admin/users` | `Admin\Users` | `view users` |
| `GET /admin/users/create` | `Admin\Users\CreateUser` | `create users` |
| `GET /admin/users/{user}` | `Admin\Users\ViewUser` | `view users` |
| `GET /admin/users/{user}/edit` | `Admin\Users\EditUser` | `update users` |
| `GET /admin/roles` | `Admin\Roles` | `view roles` |
| `GET /admin/roles/create` | `Admin\Roles\CreateRole` | `create roles` |
| `GET /admin/roles/{role}/edit` | `Admin\Roles\EditRole` | `update roles` |
| `GET /admin/permissions` | `Admin\Permissions` | `view permissions` |
| `GET /admin/permissions/create` | `Admin\Permissions\CreatePermission` | `create permissions` |
| `GET /admin/permissions/{permission}/edit` | `Admin\Permissions\EditPermission` | `update permissions` |

Delete actions are handled inside the list components (`Admin\Users`, `Admin\Roles`, `Admin\Permissions`) and are authorized against `delete users`, `delete roles`, and `delete permissions` respectively.

### Managing Users

1. Navigate to `/admin/users`
2. Search users by name or email, or filter by role
3. Click **Create** to add a new user - set name, email, locale, and roles. New users are created with a random password (they can use the password reset flow to set their own)
4. Click **Edit** on a user to update their details and sync their roles
5. Click **Delete** to remove a user

### Managing Roles

1. Navigate to `/admin/roles`
2. Click **Create** to add a role - give it a name and select at least one permission
3. Click **Edit** to rename a role or change its permissions
4. Click **Delete** to remove a role

### Managing Permissions

1. Navigate to `/admin/permissions`
2. Create, edit, or delete permissions by name

**Note:** Permissions created in the UI are not automatically attached to any role - assign them via the role edit screen.

## Impersonation

Users with the `impersonate` permission can log in as any other user from the users list.

### How It Works

1. On `/admin/users`, an **Impersonate** button is shown next to each user (except yourself) for users who have the `impersonate` permission
2. Clicking it sends a `POST` to `/impersonate/{user}` (`ImpersonationController::store`), which:
   - Authorizes the `impersonate` permission
   - Stores your own user ID in the session as `admin_user_id`
   - Logs you in as the target user and redirects to the dashboard
3. While impersonating, a banner with a **stop impersonating** form is shown in the app layouts (sidebar and frontend)
4. Stopping sends a `DELETE` to `/impersonate/stop` (`ImpersonationController::destroy`), which logs you back in as the original user, clears `admin_user_id` from the session, and redirects to `/admin`

### Routes

- `POST /impersonate/{user}` - Start impersonating (`impersonate.store`, protected by `can:impersonate` middleware)
- `DELETE /impersonate/stop` - Stop impersonating (`impersonate.destroy`)

### Required Permission

Impersonation requires the `impersonate` permission. It is enforced both by the `can:impersonate` route middleware and by `$this->authorize('impersonate')` in the controller. The Super Admin role has this permission by default.

## Technical Details

### Key Files

**Console Commands:**
- `app/Console/Commands/CreateSuperAdminCommand.php` - `php artisan app:create-super-admin`

**Controllers:**
- `app/Http/Controllers/ImpersonationController.php` - Start/stop impersonation

**Livewire Components:**
- `app/Livewire/Admin/Index.php` - Admin dashboard
- `app/Livewire/Admin/Users.php` - Users list (search, role filter, delete)
- `app/Livewire/Admin/Users/CreateUser.php`
- `app/Livewire/Admin/Users/EditUser.php`
- `app/Livewire/Admin/Users/ViewUser.php`
- `app/Livewire/Admin/Roles.php` - Roles list (search, delete)
- `app/Livewire/Admin/Roles/CreateRole.php`
- `app/Livewire/Admin/Roles/EditRole.php`
- `app/Livewire/Admin/Permissions.php` - Permissions list (search, delete)
- `app/Livewire/Admin/Permissions/CreatePermission.php`
- `app/Livewire/Admin/Permissions/EditPermission.php`

**Seeders:**
- `database/seeders/PermissionSeeder.php`
- `database/seeders/RoleSeeder.php`
- `database/seeders/DatabaseSeeder.php`

**Views:**
- `resources/views/livewire/admin/` - Admin views (using the `components.layouts.admin` layout)
- `resources/views/components/layouts/app/sidebar.blade.php` - Stop-impersonating banner
- `resources/views/components/layouts/app/frontend.blade.php` - Stop-impersonating banner

**Routes:**
- `routes/web.php` - Admin and impersonation routes

**Language Files:**
- `lang/{locale}/users.php`, `lang/{locale}/roles.php`, `lang/{locale}/permissions.php`

### Authorization

Authorization is enforced in two layers:

1. **Route middleware** - e.g. `->middleware('can:view users')` in `routes/web.php`
2. **Component/controller authorization** - `$this->authorize('...')` in `mount()` and in every mutating action

This means even Livewire actions that bypass the route (e.g. delete actions on list pages) are still permission-checked.

## Testing

Test coverage for the admin system lives in:

- `tests/Feature/Livewire/Admin/IndexTest.php`
- `tests/Feature/Livewire/Admin/UsersTest.php` and `tests/Feature/Livewire/Admin/Users/`
- `tests/Feature/Livewire/Admin/RolesTest.php` and `tests/Feature/Livewire/Admin/Roles/`
- `tests/Feature/Livewire/Admin/PermissionsTest.php` and `tests/Feature/Livewire/Admin/Permissions/`
- `tests/Feature/Command/CreateSuperAdminCommandTest.php`

Run the admin-related tests:

```bash
php artisan test --filter=Admin
```

Or run all tests:

```bash
php artisan test
```

## FAQ

**Q: Does the Super Admin role automatically get new permissions?**

A: No. The role is granted all permissions that exist when `RoleSeeder` runs. If you add permissions later, assign them to the role via `/admin/roles` or re-run `php artisan db:seed`.

**Q: Can I rename or remove the seeded permissions?**

A: Yes, but the permission names are referenced by routes (`can:` middleware), component `authorize()` calls, and Blade `@can` directives. If you rename a permission, update those references too.

**Q: What password do users created in the admin get?**

A: A random 16-character password. They should use the "Forgot password" flow to set their own.

**Q: Can an impersonating admin impersonate another user while already impersonating?**

A: The impersonated user would need the `impersonate` permission for the button to appear. Note that starting a new impersonation overwrites the stored `admin_user_id`, so chained impersonation returns you to the most recent impersonator, not the original admin.

**Q: How do I check permissions in my own code?**

A: Use the standard Laravel/Spatie APIs: `$user->can('view users')`, `@can('impersonate')` in Blade, `->middleware('can:view roles')` on routes, or `$this->authorize('update users')` in components and controllers.
