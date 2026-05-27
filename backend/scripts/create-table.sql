CREATE TABLE search_history (
    id SERIAL PRIMARY KEY,
    search_term VARCHAR(500) NOT NULL,
    searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    session_id VARCHAR(45) NOT NULL
);