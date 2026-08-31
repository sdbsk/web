-- Guarantee a password-less root reachable from the host over TCP.
--
-- MYSQL_ALLOW_EMPTY_PASSWORD only clears the password for root@localhost, and
-- whether the entrypoint also creates root@'%' has varied between image
-- versions. Connections from the host arrive from the bridge gateway address,
-- so without this the DATABASE_URL in .env.local gets "Access denied".
CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
