# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Server Management
- `symfony server:start` - Start the Symfony development server
- `symfony server:stop` - Stop the Symfony development server

### Database
- `php bin/console doctrine:migrations:migrate` - Apply database migrations
- `php bin/console doctrine:fixtures:load` - Load database fixtures (includes Foundry factories)
- `php bin/console doctrine:database:create` - Create the database
- `php bin/console doctrine:schema:update --force` - Update database schema

### Testing
- `php bin/phpunit` - Run all tests
- `php bin/phpunit tests/` - Run tests in specific directory
- PHPUnit configuration is in `phpunit.dist.xml` with Foundry extension enabled

### Code Generation
- `php bin/console make:entity` - Create/update entities
- `php bin/console make:controller` - Create controllers
- `php bin/console make:form` - Create form types
- `php bin/console make:migration` - Generate migrations
- `php bin/console make:crud` - Generate CRUD operations

### Cache & Assets
- `php bin/console cache:clear` - Clear cache
- `php bin/console asset-map:compile` - Compile assets
- `composer install` - Install PHP dependencies

## Architecture Overview

### Core Structure
This is a Symfony 7.3 recipe management application with the following key components:

**Entities & Domain:**
- `Recipe` entity with title, slug, content, duration, regime (enum), and timestamps
- `Category` entity with name, slug, and timestamps  
- Both entities use UniqueEntity constraints and auto-generated slugs
- Custom validator `BanWord` for content filtering
- `RecipeRegime` enum for recipe classifications

**Controllers:**
- **Public controllers:** `HomeController`, `RecipeController`, `ContactController`
- **Admin controllers:** `Admin/RecipeController`, `Admin/CategoryController`
- Admin routes are prefixed with `/admin`
- Uses attribute-based routing configuration

**Forms & Validation:**
- `RecipeType`, `CategoryType`, `ContactType` form classes
- `FormListenerFactory` for dynamic form behavior
- Comprehensive validation using Symfony constraints
- Custom `BanWordValidator` implementation

**Data Layer:**
- Doctrine ORM with custom repositories
- Database migrations in `migrations/` directory
- Foundry factories for test data generation (`AppStory`)
- `ContactDTO` for form data handling

### Frontend Architecture
- **Stimulus/Turbo:** Uses Symfony UX with Stimulus controllers and Turbo
- **Asset Management:** Symfony AssetMapper for modern asset handling
- **Templates:** Twig templates with admin layout inheritance
- **Assets:** Located in `assets/` with `app.js` and `app.css` entry points

### Key Features
- Automatic slug generation for recipes and categories
- Automatic timestamp management (createdAt/updatedAt)
- Admin interface with full CRUD operations
- Email contact form functionality
- Flash message system for user feedback
- Form validation with custom constraints

### Development Notes
- Uses PHP 8.2+ with strict typing
- Doctrine annotations replaced with PHP 8 attributes
- Service autowiring enabled by default
- Environment-specific configuration in `config/packages/`
- Translation support configured but not actively used

### Testing Setup
- PHPUnit with Foundry extension for entity factories
- Test database configuration in `phpunit.dist.xml`
- Bootstrap file at `tests/bootstrap.php`
- Foundry stories for consistent test data