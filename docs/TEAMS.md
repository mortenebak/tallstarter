# Teams Feature

This starter kit includes a comprehensive teams functionality that allows users to create, manage, and collaborate within teams. Teams can be enabled or disabled via configuration, making it suitable for both single-user and multi-team applications.

## Features

- **Configurable feature** - Enable or disable teams functionality via configuration
- **Team creation** - Users can create new teams with custom names and descriptions
- **Team switching** - Easy switching between teams a user belongs to
- **Role-based access** - Two roles: Admin and Member
  - Admins can manage team members, roles, and invitations
  - Members can view team information but cannot manage settings
- **Member management** - Add and remove team members
- **Role management** - Change member roles with safety constraints
  - At least one admin must always exist
  - Cannot remove the last admin
  - Cannot remove yourself from the team
- **Email invitations** - Invite users to teams via email with secure tokens
- **Pending invitations** - Track and manage pending invitations
- **Current team tracking** - Each user has a current active team

## Configuration

Teams functionality can be enabled or disabled via the configuration file. By default, teams are disabled.

### Enabling Teams

Set the `TEAMS_ENABLED` environment variable in your `.env` file:

```env
TEAMS_ENABLED=true
```

Or modify `config/teams.php`:

```php
return [
    'enabled' => true,
];
```

When teams are disabled, all team-related routes will return 404 errors.

## User Guide

### Accessing Teams

1. Log in to your account
2. Click on your profile avatar in the top-right corner
3. Select **Teams** from the dropdown menu (only visible when teams are enabled)
4. You'll be redirected to the Teams page

### Creating a Team

1. Navigate to the Teams page
2. Click **Create Team** button
3. Enter a team name (required)
4. Optionally add a description
5. Click **Create**
6. You'll be automatically added as an admin and set as your current team (if you don't have one)

### Switching Teams

1. Go to the Teams page
2. You'll see a list of all teams you're a member of
3. Your current team will be highlighted
4. Click **Switch Team** on any team to make it your active team
5. You can only switch to teams you're a member of

### Managing a Team

Only team admins can manage team settings. To access team management:

1. Go to the Teams page
2. Click **Manage** on any team where you're an admin
3. You'll see the team management page with:
   - **Members list** - All current team members with their roles
   - **Pending invitations** - Invitations that haven't been accepted yet
   - **Invite user form** - Send new invitations

### Inviting Users to a Team

Team admins can invite users by email:

1. Navigate to the team management page
2. In the "Invite User" section, enter an email address
3. Select a role (Admin or Member)
4. Click **Send Invitation**
5. An email with an invitation link will be sent to the user
6. The invitation expires after 7 days

### Accepting Team Invitations

When you receive a team invitation email:

1. Click the **Accept Invitation** link in the email
2. You'll be redirected to the teams page and automatically added to the team
3. If you don't have a current team, the invited team will be set as your current team
4. The invitation will be removed after acceptance

**Note:** You must be logged in to accept an invitation. The email address must match your account email.

### Changing Member Roles

Team admins can change member roles:

1. Go to team management
2. Find the member you want to change
3. Click **Edit** on their role
4. Select a new role (Admin or Member)
5. Click **Update**
6. The change will be applied immediately

**Restrictions:**
- You cannot demote the last admin to member
- There must always be at least one admin per team

### Removing Team Members

Team admins can remove members:

1. Go to team management
2. Find the member you want to remove
3. Click **Remove**
4. Confirm the removal
5. The member will be immediately removed from the team

**Restrictions:**
- You cannot remove yourself from a team
- You cannot remove the last admin
- If the removed member's current team is this team, their current team will be cleared

## Technical Details

### Database Schema

Teams functionality uses the following database tables:

#### `teams` Table
- `id` - Primary key
- `name` - Team name (required)
- `slug` - URL-friendly team identifier (auto-generated, unique)
- `description` - Optional team description
- `timestamps` - Created and updated timestamps

#### `team_user` Table (Pivot)
- `id` - Primary key
- `team_id` - Foreign key to teams
- `user_id` - Foreign key to users
- `role` - Member role ('admin' or 'member', default: 'member')
- `timestamps` - Created and updated timestamps
- Unique constraint on `(team_id, user_id)`

#### `team_invitations` Table
- `id` - Primary key
- `team_id` - Foreign key to teams
- `email` - Email address of the invited user
- `role` - Role assigned upon acceptance ('admin' or 'member', default: 'member')
- `token` - Unique secure token for invitation acceptance (32 characters)
- `expires_at` - Expiration timestamp (default: 7 days from creation)
- `timestamps` - Created and updated timestamps

#### `users` Table (Modified)
- `current_team_id` - Foreign key to teams (nullable) - The user's currently active team

### Models

#### `App\Models\Team`
- Relationships: `users()`, `invitations()`, `admins()`, `members()`
- Methods: `isAdmin(User $user)`, `hasMember(User $user)`, `getRoleForUser(User $user)`
- Auto-generates slug from name on creation

#### `App\Models\TeamInvitation`
- Relationships: `team()`, `user()` (by email)
- Methods: `accept()` - Adds user to team and deletes invitation

#### `App\Models\User` (Extended)
- Relationships: `teams()`, `currentTeam()`
- Methods: `belongsToTeam(Team $team)`, `getRoleInTeam(Team $team)`, `isAdminOfTeam(Team $team)`

### Livewire Actions

All team operations are handled through dedicated action classes:

- `App\Livewire\Actions\CreateTeam` - Creates a new team and adds creator as admin
- `App\Livewire\Actions\SwitchTeam` - Changes user's current team
- `App\Livewire\Actions\InviteUserToTeam` - Creates and sends team invitation
- `App\Livewire\Actions\RemoveUserFromTeam` - Removes a user from a team
- `App\Livewire\Actions\ChangeTeamMemberRole` - Updates a member's role
- `App\Livewire\Actions\AcceptTeamInvitation` - Accepts a team invitation

### Livewire Components

- `App\Livewire\Teams\Index` - List teams and switch between them
- `App\Livewire\Teams\CreateTeam` - Create a new team form
- `App\Livewire\Teams\ManageTeam` - Manage team members, roles, and invitations

### Routes

All team routes are prefixed with `/teams` and require authentication:

- `GET /teams` - List teams (Teams\Index)
- `GET /teams/create` - Create team form (Teams\CreateTeam)
- `GET /teams/{team}` - Manage team (Teams\ManageTeam)
- `GET /teams/invitations/{invitation}/accept` - Accept invitation (signed route)

All routes check if teams are enabled and return 404 if disabled.

### Notifications

- `App\Notifications\TeamInvitationNotification` - Email notification sent when inviting users
  - Includes a signed URL valid for 7 days
  - Uses translation keys from `lang/{locale}/teams.php`

### Authorization

- Team membership is required to view/manage a team
- Only team admins can:
  - Invite new members
  - Change member roles
  - Remove members
- System enforces:
  - At least one admin must exist
  - Cannot remove the last admin
  - Cannot remove yourself

## Development

### Key Files

**Configuration:**
- `config/teams.php` - Teams configuration

**Migrations:**
- `database/migrations/2026_01_10_081143_create_teams_table.php`
- `database/migrations/2026_01_10_081146_create_team_invitations_table.php`
- `database/migrations/2026_01_10_081204_create_team_user_table.php`
- `database/migrations/2026_01_10_081211_add_current_team_id_to_users_table.php`

**Models:**
- `app/Models/Team.php`
- `app/Models/TeamInvitation.php`
- `app/Models/User.php` (extended)

**Factories:**
- `database/factories/TeamFactory.php`

**Livewire Actions:**
- `app/Livewire/Actions/CreateTeam.php`
- `app/Livewire/Actions/SwitchTeam.php`
- `app/Livewire/Actions/InviteUserToTeam.php`
- `app/Livewire/Actions/RemoveUserFromTeam.php`
- `app/Livewire/Actions/ChangeTeamMemberRole.php`
- `app/Livewire/Actions/AcceptTeamInvitation.php`

**Livewire Components:**
- `app/Livewire/Teams/Index.php`
- `app/Livewire/Teams/CreateTeam.php`
- `app/Livewire/Teams/ManageTeam.php`

**Views:**
- `resources/views/livewire/teams/index.blade.php`
- `resources/views/livewire/teams/create-team.blade.php`
- `resources/views/livewire/teams/manage-team.blade.php`

**Language Files:**
- `lang/en/teams.php`
- `lang/da/teams.php`

**Routes:**
- `routes/web.php` (teams routes section)

**Navigation:**
- `resources/views/components/layouts/app/sidebar.blade.php` (teams link)
- `resources/views/components/layouts/app/header.blade.php` (teams link)
- `resources/views/components/layouts/app/frontend.blade.php` (teams link)

### Testing

Comprehensive test coverage is provided for all team functionality:

**Feature Tests:**
- `tests/Feature/Teams/IndexTest.php` - Teams listing and switching
- `tests/Feature/Teams/CreateTeamTest.php` - Team creation
- `tests/Feature/Teams/ManageTeamTest.php` - Team management
- `tests/Feature/Teams/Actions/CreateTeamActionTest.php` - Create team action
- `tests/Feature/Teams/Actions/SwitchTeamActionTest.php` - Switch team action
- `tests/Feature/Teams/Actions/InviteUserToTeamActionTest.php` - Invitation action
- `tests/Feature/Teams/Actions/RemoveUserFromTeamActionTest.php` - Remove member action
- `tests/Feature/Teams/Actions/ChangeTeamMemberRoleActionTest.php` - Change role action
- `tests/Feature/Teams/Actions/AcceptTeamInvitationActionTest.php` - Accept invitation action

Run team-related tests:

```bash
php artisan test --filter=Teams
```

Or run all tests:

```bash
php artisan test
```

## FAQ

**Q: Can teams be disabled after being enabled?**

A: Yes, you can disable teams by setting `TEAMS_ENABLED=false` in your `.env` file. All team routes will return 404, but existing team data will remain in the database.

**Q: What happens if a user's current team is deleted?**

A: The `current_team_id` foreign key has `nullOnDelete()` constraint, so if a team is deleted, the user's `current_team_id` will be set to `null`. They'll need to select a new current team.

**Q: Can a user belong to multiple teams?**

A: Yes, users can belong to multiple teams. Each user has one "current team" that they can switch between.

**Q: What happens to pending invitations when a team is deleted?**

A: The `team_invitations` table has a `cascadeOnDelete()` foreign key, so all invitations are automatically deleted when a team is deleted.

**Q: Can invitations expire?**

A: Yes, invitations expire after 7 days by default. Expired invitations cannot be accepted and should be cleaned up periodically.

**Q: How do I clean up expired invitations?**

A: You can create a scheduled command to delete expired invitations:

```php
TeamInvitation::where('expires_at', '<', now())->delete();
```

**Q: Can team names be duplicated?**

A: Yes, team names can be duplicated, but slugs (URL-friendly identifiers) must be unique. The system auto-generates slugs from names.

**Q: Is there a limit on team size?**

A: No, there's no built-in limit on the number of members per team. You can add this in the `InviteUserToTeam` action if needed.

**Q: Can I customize the invitation email?**

A: Yes, modify the `App\Notifications\TeamInvitationNotification` class and its associated translations in `lang/{locale}/teams.php`.

## Future Enhancements

Potential improvements that could be added:

- Team-specific permissions and roles
- Team settings (custom branding, preferences)
- Team activity logs
- Team notifications
- Bulk invitation via CSV upload
- Team templates
- Team archives
- Team statistics and analytics
- Direct user addition (without invitation) for admins
- Team ownership transfer
- Team deletion with data export
