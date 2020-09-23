
### Prerequisites

This is website for tools of Dotmark Connect. It is building base on an open-source [Laravue](https://github.com/tuandm/laravue)

### Installing
```bash
# Clone the project and run composer
composer create-project tuandm/laravue
cd laravue

# Generate permission for folder to read and write log file
sudo chmod -R 777 /path-to-file/lavavue/storage

# Generate environtment variables file:
cp .env.sample .env

# Changing database setting in .env
nano .env (change db to useful configs)

# Create Output and Upload directories
mkdir /path-to-file/lavavue/public/output
mkdir /path-to-file/lavavue/public/upload

# Migration and DB seeder (after changing your DB settings in .env)
php artisan migrate --seed

# Install dependency with NPM
npm install

# develop
npm run dev # or npm run watch

# Build on production
npm run production
