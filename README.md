## About Foodies

Foodies is an online food ordering platform built using the Laravel framework. The website allows users to register as customers to conveniently order food online or as restaurant owners to offer takeout services. Additionally, it includes an adminstration feature that manages the approval process for newly registered restaurant owners, ensuring a smooth and reliable onboarding experience.

## Special Features

### Dish Recommendation List
Users who have five or more dishes saved in their Favourite List get personalised dish recommendations tailored to their preferences. A scoring system is implemented to generate the list. Scores are assigned to each dish based on its similarity to those saved in the Favourite List. The top five dishes with the highest scores will be recommended to the user, providing a delightful user experience that aligns with the user's taste profile.

### Popular Dish List
This section provides the top five most sought after dishes ordered within the past 30 days.

### Admin Role
The admin user type serves a unique role, that is to approve newly registered restaurant accounts. Only after receiving approval can the restaurant owners create and edit their dish menus.

## Technologies Used
- Language: PHP
- Framework: Laravel, Bootstrap
- Database: SQLite

## Installation
1. Run `npm install` to install npm packages and dependencies.
2. Run `php artisan migrate` to create tables in database.
3. Run `php artisan db:seed` to seed database with dummy data.
4. Run `php artisan serve` to launch the website.

## Developer
Loo<br>
loo.workspace@gmail.com