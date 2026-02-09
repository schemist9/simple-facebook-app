CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    firstname TEXT NOT NULL,
    surname TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS posts (
    id SERIAL PRIMARY KEY,
    text TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    user_wall_id INT REFERENCES users(id),
    user_id INT REFERENCES users(id) 
);

CREATE TABLE IF NOT EXISTS posts_likes (
    id SERIAL PRIMARY KEY,
    post_id INT REFERENCES posts(id),
    user_id INT REFERENCES users(id)
);
/*
later: explore JTI implementation
*/
CREATE TABLE IF NOT EXISTS comments (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    text TEXT NOT NULL,
    commentable_id INT NOT NULL,
    commentable_type TEXT NOT NULL
);