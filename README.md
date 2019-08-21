# notes-gallery
Notes gallery created using core PHP

This project is created by using core PHP, and with some packages installed by using composer. Therefore, this project is also using composer's autoloader to implement PHP autoload function.

## Get Started
Firstly, create an .env file and populate your database configurations. Next, create database tables with the following fields:

- `users` table

|   Field   |     Type     | Null | Key |
| --------- | ------------ | ----:| ---:|
| id        | varchar(255) |  NO  | PRI |
| username  | varchar(255) |  YES |     |
| password  | varchar(255) |  YES |     |
| token     | varchar(255) |  YES |     |
| avatarUrl | varchar(512) |  YES |     |

- `notes` table

|     Field   |     Type     | Null | Key |
| ----------- | ------------ | ----:| ---:|
| id          | varchar(255) |  NO  | PRI |
| title       | varchar(255) |  YES |     |
| content     | TEXT         |  YES |     |
| description | TEXt         |  YES |     |
| authorId    | varchar(512) |  YES | MUL |

**IMPORTANT: The two tables' name must be `users` and `notes` (case-sensitive) to get the app starts correctly.**

After created two tables above, you may populate data with fake data as you like.

Then, we can start the server now to get notes-gallery up and running. Since this is a simple project, I will just stick with PHP default server:

```bash
cd notes-gallery
php -S localhost:8080 -t public/
```

This will get the app up and running on the port `8080` on `localhost`.
