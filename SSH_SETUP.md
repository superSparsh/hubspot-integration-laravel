# SSH Key Setup for HubSpot Integration Laravel

This project has a unique SSH key for GitHub authentication.

## SSH Key Location

-   **Private Key**: `ssh-keys/id_ed25519_hubspot`
-   **Public Key**: `ssh-keys/id_ed25519_hubspot.pub`

## Setup Instructions

### 1. Add SSH Key to GitHub

1. Copy the public key:

    ```bash
    cat ssh-keys/id_ed25519_hubspot.pub
    ```

2. Go to GitHub → Settings → SSH and GPG keys → New SSH key
3. Paste the public key and save

### 2. Configure SSH for This Repository

Add this to your `~/.ssh/config`:

```
Host github.com-hubspot-laravel
    HostName github.com
    User git
    IdentityFile /home/developer/Documents/laravel-hubspot/ssh-keys/id_ed25519_hubspot
    IdentitiesOnly yes
```

### 3. Update Git Remote (if needed)

If you need to use the SSH config alias:

```bash
git remote set-url origin git@github.com-hubspot-laravel:superSparsh/hubspot-integration-laravel.git
```

## Testing SSH Connection

```bash
ssh -T -i ssh-keys/id_ed25519_hubspot git@github.com
```

You should see: "Hi superSparsh! You've successfully authenticated..."

## First Push

```bash
git add .
git commit -m "Initial commit: Laravel HubSpot Integration"
git branch -M main
git push -u origin main
```

## Security Notes

⚠️ **IMPORTANT**:

-   The `ssh-keys/` directory is in `.gitignore` and will NOT be committed
-   Never commit private keys to the repository
-   Keep your SSH keys secure and backed up separately
