CREATE TABLE IF NOT EXISTS session_requests (
    id INT NOT NULL AUTO_INCREMENT,
    student_id INT NOT NULL,
    mentor_id INT NOT NULL,
    session_datetime DATETIME NOT NULL,
    duration SMALLINT UNSIGNED NOT NULL,
    session_type ENUM('Video call', 'Chat Session') NOT NULL,
    status ENUM('pending', 'accepted', 'declined', 'completed') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_session_requests_student (student_id),
    KEY idx_session_requests_mentor_created (mentor_id, created_at),
    CONSTRAINT fk_session_requests_student
        FOREIGN KEY (student_id) REFERENCES studentstb (student_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_session_requests_mentor
        FOREIGN KEY (mentor_id) REFERENCES mentorstb (mentor_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
