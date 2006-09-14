ALTER TABLE `recurrence` ADD `recurrence_pattern_id` INT NOT NULL ;
ALTER TABLE `recurrence_pattern` CHANGE `pattern_type` `pattern_type` 
ENUM( 'day', 'monthweek', 'monthday', 'yearmonthweek', 'yearmonthday', 'dayweek' ) NOT NULL DEFAULT 'day' ;
