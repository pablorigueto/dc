-- Active: 1676988464928@@127.0.0.1@32769@drupal10
CREATE TABLE clt_offer_table (
    id int(20) NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    create_time VARCHAR(30),
    nodeId INT(20),
    name_offer VARCHAR(50),
    email_offer VARCHAR(50),
    phone_offer VARCHAR(20),
    bday_offer VARCHAR(20),
    down_offer VARCHAR(20),
    clt_id VARCHAR(20),
    checkbox_with_car_offer BOOLEAN,
    clt_debits_offer BOOLEAN,
    clt_extra_info VARCHAR(255),
    clt_car_brand_offer VARCHAR(50),
    clt_car_model_offer VARCHAR(50),
    clt_car_gearshift_offer VARCHAR(50),
    clt_car_fuel_offer VARCHAR(50),
    clt_car_kmormiles_offer VARCHAR(50),
    clt_car_color_offer VARCHAR(50),
    clt_car_sttyear_offer VARCHAR(20),
    clt_car_endyear_offer VARCHAR(20),
    clt_car_id_offer INT(25)
) COMMENT '';