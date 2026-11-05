CREATE TABLE IF NOT EXISTS session_requests (
    session_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    student_id INT NOT NULL,
    mentor_id INT NOT NULL,
    session_datetime DATETIME NOT NULL,
    duration SMALLINT UNSIGNED NOT NULL,
    session_type VARCHAR(32) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (session_id),
    KEY idx_session_requests_student (student_id),
    KEY idx_session_requests_mentor (mentor_id)
);
