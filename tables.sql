CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    firstname TEXT NOT NULL,
    surname TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    avatar TEXT DEFAULT '/data/default-avatar.jpg',
    password_hash TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS posts (
    id SERIAL PRIMARY KEY,
    text TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL,
    user_wall_id INT REFERENCES users(id),
    user_id INT REFERENCES users(id) 
);

-- CREATE TABLE IF NOT EXISTS posts_likes (

CREATE TABLE IF NOT EXISTS likes (
    id SERIAL PRIMARY KEY,
    -- post_id INT REFERENCES posts(id),
    user_id INT REFERENCES users(id),
    likeable_id INT NOT NULL,
    likeable_type TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL
);
/*
later: explore JTI implementation
*/
CREATE TABLE IF NOT EXISTS comments (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id),
    text TEXT NOT NULL,
    commentable_id INT NOT NULL,
    commentable_type TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL
);


CREATE TABLE IF NOT EXISTS friend_requests (
    id SERIAL PRIMARY KEY,
    from_id INT REFERENCES users(id),
    to_id INT REFERENCES users(id),
    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL
);

CREATE TABLE IF NOT EXISTS users_friendships (
    id SERIAL PRIMARY KEY,
    user_1 INT REFERENCES users(id),
    user_2 INT REFERENCES users(id),
    created_at TIMESTAMPTZ DEFAULT NOW() NOT NULL
);