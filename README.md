# README

File upload demo with authentication.
See screenshots [here](/screenshots).

## Setup
1. Create the database `filesend`
2. Modify `src/config/db.php` based on your DB settings
3. Run Apache and MySQL Server
4. Create tables using the following queries

```sql
CREATE TABLE Users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE Files (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    userid INT NOT NULL,
    file_name VARCHAR(50) NOT NULL,
    file_size VARCHAR(255) NOT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_userid
       FOREIGN KEY (userid)
       REFERENCES Users(id)
       ON DELETE CASCADE,
    UNIQUE (userid, file_name)
);
```

## Assumptions
- An email can only be used for registration once.
- Assume unique file names under the same user directory.

## Tools and Components
- MAMP
- Apache/2.4.43
- PHP/7.4.2
- MySQL/5.7.26

## References

- [MAMP, MAMP PRO, NAMO & appdoo Documentation](https://documentation.mamp.info/)
- [PHP: Language Reference - Manual](https://www.php.net/manual/en/langref.php)
- [PHP MySQLi Prepared Statements Tutorial to Prevent SQL Injection](https://websitebeaver.com/prepared-statements-in-php-mysqli-to-prevent-sql-injection)
- [PHP Sessions - W3Schools](https://www.w3schools.com/php/php_sessions.asp)
