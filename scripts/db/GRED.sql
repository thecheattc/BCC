INSERT INTO bcc_food_client.genders(gender_desc)
VALUES ('Male'), ('Female');

INSERT INTO bcc_food_client.ethnicities(ethnicity_desc)
VALUES ('White'), ('Black'), ('Hispanic'),
('Asian or Pacific Islander'), ('American Indian');

INSERT INTO bcc_food_client.reasons(reason_desc)
VALUES ('Lost job'), ('Unusual expenses this month'),
('To make ends meet'), ('Assistance lost/reduced'),
('DHS application in progress'), ('Homeless'),
('Other');

INSERT INTO bcc_food_client.distribution_type(dist_type_desc) VALUES ('Normal'), ('Emergency'), ('Rejected');