<?php

class m130619_082153_create_initial_db extends CDbMigration {

    public function up()
    {
        // Ist die DB leer (enhält nur tbl_migration)?
        if (count($this->getDbConnection()->getSchema()->getTableNames()) !== 1) {
            echo "Diese Migration kann nur auf eine leere DB angewendet werden.\nUeberspringe Migration...\n";
            return;
        }

        // Schema
        $this->execute("
            -- common for all users of the application
            CREATE TABLE ta_users (
              id int NOT NULL IDENTITY,
              -- email is used as login username
              email varchar(255) NOT NULL UNIQUE,
              password varchar(255) NOT NULL,
              activkey varchar(255) NOT NULL DEFAULT '',
              role varchar(255) NOT NULL,
              created_at datetime NOT NULL,
              lastvisit_at datetime NOT NULL,
              PRIMARY KEY (id)
            );

            -- different customer categories (e.g. internal, etc.)
            CREATE TABLE ta_customer_categories (
              id int NOT NULL IDENTITY,
              name varchar(255) NOT NULL UNIQUE,
              description text NOT NULL,
              PRIMARY KEY (id)
            );

            -- customer data
            CREATE TABLE ta_customers (
              id int NOT NULL IDENTITY,
              user_id int DEFAULT NULL,
              lastname varchar(255) NOT NULL,
              firstname varchar(255) NOT NULL,
              birthdate date NOT NULL,
              address varchar(255) NOT NULL,
              postcode int NOT NULL,
              city varchar(255) NOT NULL,
              -- mobile is used to receive text messages
              mobile varchar(16) NOT NULL,
              phone varchar(16) NOT NULL,
              -- customers from different categories can have different prices for products
              category int DEFAULT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_customers_user_id
                FOREIGN KEY (user_id)
                REFERENCES ta_users (id)
                ON DELETE CASCADE
                ON UPDATE SET DEFAULT,
              CONSTRAINT ta_customers_category
                FOREIGN KEY (category)
                REFERENCES ta_customer_categories (id)
                ON DELETE CASCADE
                ON UPDATE SET DEFAULT
            );

            -- all the countries in the world, plus a few disputed regions
            CREATE TABLE ta_countries (
              id int NOT NULL IDENTITY,
              name varchar(255) NOT NULL UNIQUE,
              PRIMARY KEY (id)
            );

            -- different trip types (e.g. backpacking, trekking, safari, business, around-the-world, etc.)
            CREATE TABLE ta_trip_types (
              id int NOT NULL IDENTITY,
              name varchar(255) NOT NULL UNIQUE,
              description text NOT NULL,
              PRIMARY KEY (id)
            );

            -- All diseases
            CREATE TABLE ta_diseases (
              id int NOT NULL IDENTITY,
              name varchar(255) NOT NULL UNIQUE,
              PRIMARY KEY (id)
            );

            -- Vaccination status of a customer
            CREATE TABLE ta_vaccination_status (
              id int NOT NULL IDENTITY,
              customer_id int NOT NULL,
              disease_id int DEFAULT NULL,
              disease_free_text VARCHAR(255) DEFAULT NULL,
              protection decimal(5, 2) NOT NULL,
              last_vaccination DATE DEFAULT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_vaccination_status_customer_id
                FOREIGN KEY (customer_id)
                REFERENCES ta_customers (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_vaccination_status_disease_id
                FOREIGN KEY (disease_id)
                REFERENCES ta_diseases (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );

            CREATE TABLE ta_trips (
              id int NOT NULL IDENTITY,
              customer_id int NOT NULL,
              -- start/end of the trip
              start date NOT NULL,
              [end] date NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_trips_customer_id
                FOREIGN KEY (customer_id)
                REFERENCES ta_customers (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );

            -- all destinations the customer is visiting on the trip
            CREATE TABLE ta_trips_countries (
              trip_id int NOT NULL,
              country_id int NOT NULL,
              PRIMARY KEY (trip_id, country_id),
              CONSTRAINT ta_trips_countries_trip_id
                FOREIGN KEY (trip_id)
                REFERENCES ta_trips (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_trips_countries_country_id
                FOREIGN KEY (country_id)
                REFERENCES ta_countries (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );

            -- all types of the trip
            CREATE TABLE ta_trips_trip_types (
              trip_id int NOT NULL,
              type_id int NOT NULL,
              PRIMARY KEY (trip_id, type_id),
              CONSTRAINT ta_trips_trip_types_trip_id
                FOREIGN KEY (trip_id)
                REFERENCES ta_trips (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_trips_trip_types_type_id
                FOREIGN KEY (type_id)
                REFERENCES ta_trip_types (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );

            -- Additional people that my accompany the customer on a trip. All people added
            -- to a specific trip will be stored here, so they can be suggested for later
            -- trips.
            CREATE TABLE ta_companions (
              id int NOT NULL IDENTITY,
              -- reference to the customer who 'owns' the companion
              owner_customer_id int NOT NULL,
              -- basic data about the companion
              lastname varchar(255) NOT NULL DEFAULT '',
              firstname varchar(255) NOT NULL DEFAULT '',
              birthdate date NOT NULL DEFAULT '0000-00-00',
              address varchar(255) NOT NULL DEFAULT '',
              city varchar(255) NOT NULL DEFAULT '',
              postcode int NOT NULL DEFAULT '',
              -- mobile is used to receive text messages
              mobile varchar(16) NOT NULL,
              phone varchar(16) NOT NULL,
              -- (optional) reference to the full customer record of this companion
              self_customer_id int DEFAULT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_companions_owner_customer_id
                FOREIGN KEY (owner_customer_id)
                REFERENCES ta_customers (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_companions_self_customer_id
                FOREIGN KEY (self_customer_id)
                REFERENCES ta_customers (id)
                -- Nicht möglich unter MSSQL:
                -- ON DELETE SET DEFAULT
                -- ON UPDATE CASCADE
            );

            -- The relation of companions joining the customer on an actual trip.
            CREATE TABLE ta_trips_companions (
              trip_id int NOT NULL,
              companion_id int NOT NULL,
              PRIMARY KEY (trip_id, companion_id),
              CONSTRAINT ta_trips_companions_trip_id
                FOREIGN KEY (trip_id)
                REFERENCES ta_trips (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_trips_companions_companion_id
                FOREIGN KEY (companion_id)
                REFERENCES ta_companions (id)
                -- Nicht möglich unter MSSQL:
                -- ON DELETE CASCADE
                -- ON UPDATE CASCADE
            );

            -- All products
            CREATE TABLE ta_products (
              id int NOT NULL IDENTITY,
              name varchar(255) NOT NULL UNIQUE,
              -- normal price
              price decimal(9, 2) NOT NULL,
              useForTravelAdvice bit NOT NULL,
              PRIMARY KEY (id)
            );

            -- Special Product Prices
            CREATE TABLE ta_special_prices (
              id int NOT NULL IDENTITY,
              product_id int NOT NULL,
              customer_category int NOT NULL,
              price decimal(9, 2) NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_product_prices_product_id
                FOREIGN KEY (product_id)
                REFERENCES ta_products (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_product_prices_customer_category
                FOREIGN KEY (customer_category)
                REFERENCES ta_customer_categories (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );

            -- Effect of a Product against a disease (-> Vaccination)
            CREATE TABLE ta_product_vaccination_effect (
              id int NOT NULL IDENTITY,
              product_id int NOT NULL,
              disease_id int NOT NULL,
              effect decimal(4, 2) NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_vaccinations_product_id
                FOREIGN KEY (product_id)
                REFERENCES ta_products (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_vaccinations_disease_id
                FOREIGN KEY (disease_id)
                REFERENCES ta_diseases (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );

            CREATE TABLE ta_settings (
              name varchar(255) NOT NULL,
              value varchar(255) NOT NULL,
              PRIMARY KEY (name)
            );

            CREATE TABLE ta_weekday_timeframes (
              id int NOT NULL IDENTITY,
              weekday int NOT NULL,
              start time(0) NOT NULL,
              [end] time(0) NOT NULL,
              PRIMARY KEY (id)
            );

            CREATE TABLE ta_date_timeframes (
              id int NOT NULL IDENTITY,
              date date NOT NULL,
              start time(0) NOT NULL,
              [end] time(0) NOT NULL,
              PRIMARY KEY (id)
            );

            CREATE TABLE ta_case_numbers (
                id int NOT NULL IDENTITY,
                year int NOT NULL,
                month int NOT NULL,
                number int NOT NULL,
                PRIMARY KEY (id)
            );

            CREATE TABLE ta_bills (
              id int NOT NULL IDENTITY,
              customer_id int DEFAULT NULL,
              date datetime NOT NULL,
              -- payment method, NULL if not paid
              paid varchar(255) DEFAULT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_bills_customer_id
                FOREIGN KEY (customer_id)
                REFERENCES ta_customers (id)
                ON DELETE SET DEFAULT
                ON UPDATE CASCADE,
            );
            -- individual items
            CREATE TABLE ta_bill_items (
              id int NOT NULL IDENTITY,
              bill_id int NOT NULL,
              product_id int DEFAULT NULL,
              -- actual product attributes (must stay the same even if the product changes)
              product_name varchar(255) NOT NULL,
              price decimal(9, 2) NOT NULL,
              count int NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_bill_items_bill_id
                FOREIGN KEY (bill_id)
                REFERENCES ta_bills (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_bill_items_product_id
                FOREIGN KEY (product_id)
                REFERENCES ta_products (id)
                ON DELETE SET DEFAULT
                ON UPDATE CASCADE
            );

            -- all customer appointments
            CREATE TABLE ta_appointments (
              id int NOT NULL IDENTITY,
              customer_id int DEFAULT NULL,
              -- date and time of the appointment (optional)
              date datetime DEFAULT NULL,
              -- if the date isn't set: an optional value specifying the last day the date can be specified by the customer
              provisory_date date DEFAULT NULL,
              -- estimated duration of the appointment (in minutes)
              duration int NOT NULL,
              -- optional reference to the trip this appointment is for
              trip_id int DEFAULT NULL,
              -- reason for the appointment: Impfberatung, Nachimpfung, etc
              reason varchar(255) NOT NULL,
              -- actual time the consultation has started/ended
              started datetime DEFAULT NULL,
              ended datetime DEFAULT NULL,
              -- doctor or MPA?
              staff_type varchar(255) DEFAULT NULL,
              is_initial_appointment bit NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_appointments_customer_id
                FOREIGN KEY (customer_id)
                REFERENCES ta_customers (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_appointments_trip_id
                FOREIGN KEY (trip_id)
                REFERENCES ta_trips (id)
                -- Nicht möglich unter MSSQL:
                -- ON DELETE SET DEFAULT
                -- ON UPDATE CASCADE
            );

            CREATE TABLE ta_consultations (
              id int NOT NULL IDENTITY,
              appointment_id int NOT NULL,
              customer_id int NOT NULL,
              remarks text NOT NULL,
              bill_id int DEFAULT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_consultations_appointment_id
                FOREIGN KEY (appointment_id)
                REFERENCES ta_appointments (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_consultations_customer_id
                FOREIGN KEY (customer_id)
                REFERENCES ta_customers (id),
                -- Nicht möglich unter MSSQL:
                -- ON DELETE CASCADE
                -- ON UPDATE CASCADE,
              CONSTRAINT ta_consultations_bill_id
                FOREIGN KEY (bill_id)
                REFERENCES ta_bills (id)
                -- Nicht möglich unter MSSQL:
                -- ON DELETE SET DEFAULT
                -- ON UPDATE CASCADE
            );

            CREATE TABLE ta_consultation_products (
              id int NOT NULL IDENTITY,
              consultation_id int NOT NULL,
              product_id int NOT NULL,
              count int NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_consultation_products_consultation_id
                FOREIGN KEY (consultation_id)
                REFERENCES ta_consultations (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_consultation_products_product_id
                FOREIGN KEY (product_id)
                REFERENCES ta_products (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );

            CREATE TABLE ta_consultation_vaccinations (
              id int NOT NULL IDENTITY,
              consultation_id int NOT NULL,
              product_id int NOT NULL,
              comment text NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_consultation_vaccinations_consultation_id
                FOREIGN KEY (consultation_id)
                REFERENCES ta_consultations (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_consultation_vaccinations_product_id
                FOREIGN KEY (product_id)
                REFERENCES ta_products (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );

            CREATE TABLE ta_consultation_revaccinations (
              id int NOT NULL IDENTITY,
              vaccination_id int NOT NULL,
              consultation_id int NOT NULL,
              next_appointment_id int DEFAULT NULL,
              -- number of days between initial vaccination and revaccination
              period int NOT NULL,
              comment varchar(255) NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_consultation_revaccinations_vaccination_id
                FOREIGN KEY (vaccination_id)
                REFERENCES ta_consultation_vaccinations (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_consultation_revaccinations_consultation_id
                FOREIGN KEY (consultation_id)
                REFERENCES ta_consultations (id),
                -- ON DELETE CASCADE
                -- ON UPDATE CASCADE,
              CONSTRAINT ta_consultation_revaccinations_next_appointment_id
                FOREIGN KEY (next_appointment_id)
                REFERENCES ta_appointments (id)
                -- ON DELETE CASCADE
                -- ON UPDATE CASCADE
            );

            CREATE TABLE ta_surveys (
              id int NOT NULL IDENTITY,
              customer_id int NOT NULL,
              created_at datetime NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_survey_customer_id
                FOREIGN KEY (customer_id)
                REFERENCES ta_customers (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );

            CREATE TABLE ta_questions (
              id int NOT NULL IDENTITY,
              question varchar(255) NOT NULL,
              type varchar(255) NOT NULL,
              [order] int NOT NULL,
              active int NOT NULL,
              -- (optional) valid values
              [values] varchar(255) DEFAULT NULL,
              PRIMARY KEY (id)
            );

            CREATE TABLE ta_answers (
              id int NOT NULL IDENTITY,
              survey_id int NOT NULL,
              question_id int NOT NULL,
              -- the customers answer as a string, but must be in the type specified in questions.type
              answer varchar(255) NOT NULL,
              PRIMARY KEY (id),
              CONSTRAINT ta_answers_survey_id
                FOREIGN KEY (survey_id)
                REFERENCES ta_surveys (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
              CONSTRAINT ta_answers_question_id
                FOREIGN KEY (question_id)
                REFERENCES ta_questions (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
            );
        ");
    }

    public function down()
    {
        echo "m130619_082153_create_initial_db does not support migration down.\n";
        return false;
    }

}
