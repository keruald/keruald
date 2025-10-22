DROP TABLE IF EXISTS numbers CASCADE;
CREATE TABLE numbers
(
    id       SERIAL PRIMARY KEY,
    number    INTEGER NULL
);

DROP TABLE IF EXISTS ships CASCADE;
CREATE TABLE ships
(
    id       SERIAL PRIMARY KEY,
    name     VARCHAR(255) NULL,
    category VARCHAR(3)   NULL
);

INSERT INTO ships VALUES
                      (1, 'So Much For Subtlety', 'GSV'),
                      (2, 'Unfortunate Conflict Of Evidence', 'GSV'),
                      (3, 'Just Read The Instructions', 'GCU'),
                      (4, 'Just Another Victim Of The Ambient Morality', 'GCU');

DROP VIEW IF EXISTS ships_count CASCADE;
CREATE VIEW ships_count AS
SELECT category,
       COUNT(category) AS "count(category)"
FROM ships
GROUP BY category;
