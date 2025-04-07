-- Insert attendance records from February to current date
DELIMITER //

DROP PROCEDURE IF EXISTS PopulateAttendance //

CREATE PROCEDURE PopulateAttendance()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE curr_student_id VARCHAR(20);
    DECLARE curr_date DATE;
    DECLARE curr_status VARCHAR(10);
    DECLARE curr_teacher_id VARCHAR(10);
    
    -- Cursor for all students
    DECLARE student_cursor CURSOR FOR 
        SELECT student_id FROM students;
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Set date range from February 1st to current date
    SET curr_date = '2024-02-01';
    
    OPEN student_cursor;
    
    read_loop: LOOP
        FETCH student_cursor INTO curr_student_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        -- For each student, add attendance for each day
        WHILE curr_date <= CURDATE() DO
            -- Skip weekends
            IF DAYOFWEEK(curr_date) NOT IN (1, 7) THEN
                -- Determine teacher based on student's department and year
                SELECT 
                    CASE 
                        WHEN s.class_type = 'CIT' AND s.class_year = '1st' THEN 'CIT1'
                        WHEN s.class_type = 'CIT' AND s.class_year = '2nd' THEN 'CIT2'
                        WHEN s.class_type = 'CIT' AND s.class_year = '3rd' THEN 'CIT3'
                        WHEN s.class_type = 'ELC' AND s.class_year = '1st' THEN 'ELC1'
                        WHEN s.class_type = 'ELC' AND s.class_year = '2nd' THEN 'ELC2'
                        WHEN s.class_type = 'ELC' AND s.class_year = '3rd' THEN 'ELC3'
                        WHEN s.class_type = 'CIVIL' AND s.class_year = '1st' THEN 'CVL1'
                        WHEN s.class_type = 'CIVIL' AND s.class_year = '2nd' THEN 'CVL2'
                        WHEN s.class_type = 'CIVIL' AND s.class_year = '3rd' THEN 'CVL3'
                    END
                INTO curr_teacher_id
                FROM students s
                WHERE s.student_id = curr_student_id;
                
                -- Generate random status with weighted probability
                -- 80% Present, 15% Absent, 5% Leave
                SET curr_status = 
                    CASE 
                        WHEN RAND() < 0.80 THEN 'Present'
                        WHEN RAND() < 0.95 THEN 'Absent'
                        ELSE 'Leave'
                    END;
                
                -- Insert attendance record
                INSERT INTO attendance (student_id, date, status, teacher_id)
                VALUES (curr_student_id, curr_date, curr_status, curr_teacher_id);
            END IF;
            
            SET curr_date = DATE_ADD(curr_date, INTERVAL 1 DAY);
        END WHILE;
        
        -- Reset date for next student
        SET curr_date = '2024-02-01';
    END LOOP;
    
    CLOSE student_cursor;
END //

DELIMITER ;

-- Execute the procedure
CALL PopulateAttendance();

-- Clean up
DROP PROCEDURE IF EXISTS PopulateAttendance;