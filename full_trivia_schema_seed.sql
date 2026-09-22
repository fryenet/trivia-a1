-- Full Trivia DB Schema + Seeds (10-question system)
-- Usage:
--   mysql -u triviadb -p triviadb < full_trivia_schema_seed.sql

SET NAMES utf8mb4;
SET time_zone = '+00:00';

START TRANSACTION;

-- =======================
-- Schema
-- =======================
CREATE TABLE IF NOT EXISTS users(
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  credits INT NOT NULL DEFAULT 2,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS trivia(
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS questions(
  id INT AUTO_INCREMENT PRIMARY KEY,
  trivia_id INT NOT NULL,
  question_text TEXT NOT NULL,
  choice_a VARCHAR(255) NOT NULL,
  choice_b VARCHAR(255) NOT NULL,
  choice_c VARCHAR(255) NOT NULL,
  choice_d VARCHAR(255) NOT NULL,
  correct_choice ENUM('A','B','C','D') NOT NULL,
  FOREIGN KEY (trivia_id) REFERENCES trivia(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS games(
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  trivia_id INT NOT NULL,
  started_at DATETIME NOT NULL,
  completed_at DATETIME NULL,
  score INT NULL,
  bucket VARCHAR(20) NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (trivia_id) REFERENCES trivia(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS game_answers(
  id INT AUTO_INCREMENT PRIMARY KEY,
  game_id INT NOT NULL,
  question_id INT NOT NULL,
  selected_choice CHAR(1) NOT NULL,
  is_correct TINYINT(1) NOT NULL,
  FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
  FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =======================
-- Seeds
-- =======================

/* -------- General Knowledge (Set 1) -------- */
INSERT INTO trivia (title, is_active) VALUES ('General Knowledge', 1);
SET @tid := LAST_INSERT_ID();
INSERT INTO questions (trivia_id, question_text, choice_a, choice_b, choice_c, choice_d, correct_choice) VALUES
(@tid, 'What is the capital of France?', 'Paris', 'Berlin', 'Madrid', 'Rome', 'A'),
(@tid, 'Which planet is known as the Red Planet?', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'B'),
(@tid, 'What is 9 × 9?', '72', '81', '99', '108', 'B'),
(@tid, 'The chemical symbol for water is:', 'H2O', 'O2', 'CO2', 'NaCl', 'A'),
(@tid, 'Who wrote ''Romeo and Juliet''?', 'Charles Dickens', 'Jane Austen', 'William Shakespeare', 'Mark Twain', 'C'),
(@tid, 'Which ocean is the largest?', 'Indian', 'Atlantic', 'Arctic', 'Pacific', 'D'),
(@tid, 'In which year did the first Moon landing occur?', '1965', '1969', '1972', '1975', 'B'),
(@tid, 'What gas do plants mainly absorb?', 'Oxygen', 'Carbon Dioxide', 'Nitrogen', 'Helium', 'B'),
(@tid, 'Which language has the most native speakers?', 'English', 'Spanish', 'Mandarin Chinese', 'Hindi', 'C'),
(@tid, 'Which continent is Egypt in?', 'Asia', 'Europe', 'Africa', 'South America', 'C');

/* -------- 1990s Movies (Set 1) -------- */
INSERT INTO trivia (title, is_active) VALUES ('1990s Movies', 1);
SET @tid := LAST_INSERT_ID();
INSERT INTO questions (trivia_id, question_text, choice_a, choice_b, choice_c, choice_d, correct_choice) VALUES
(@tid, 'Which of these films was NOT released in the 1990s?', 'Titanic (1997)', 'Jurassic Park (1993)', 'Die Hard (1988)', 'The Matrix (1999)', 'C'),
(@tid, 'Who directed Titanic?', 'James Cameron', 'Steven Spielberg', 'Ridley Scott', 'Peter Jackson', 'A'),
(@tid, 'Who played Catwoman in Batman Returns (1992)?', 'Halle Berry', 'Uma Thurman', 'Michelle Pfeiffer', 'Nicole Kidman', 'C'),
(@tid, 'Which 1999 film popularized the "bullet time" effect?', 'The Matrix', 'Fight Club', 'The Sixth Sense', 'American Beauty', 'A'),
(@tid, 'Which 1994 film features the line: "Life is like a box of chocolates"?', 'Pulp Fiction', 'Forrest Gump', 'The Shawshank Redemption', 'The Lion King', 'B'),
(@tid, 'In Jurassic Park (1993), what is the name of the island where the park is located?', 'Isla Nublar', 'Isla Sorna', 'Skull Island', 'Isla Mujeres', 'A'),
(@tid, 'Who delivers the line "You can''t handle the truth!" in A Few Good Men (1992)?', 'Tom Cruise', 'Jack Nicholson', 'Kevin Bacon', 'Demi Moore', 'B'),
(@tid, 'Which 1995 Pixar film was the first full-length computer-animated feature?', 'Toy Story', 'A Bug''s Life', 'Monsters, Inc.', 'Antz', 'A'),
(@tid, 'Which 1991 thriller stars Anthony Hopkins as Hannibal Lecter?', 'Se7en', 'The Silence of the Lambs', 'The Usual Suspects', 'Heat', 'B'),
(@tid, 'In The Big Lebowski (1998), what is The Dude''s favorite drink?', 'Old Fashioned', 'White Russian', 'Martini', 'Manhattan', 'B');

/* -------- Geography (Set 1) -------- */
INSERT INTO trivia (title, is_active) VALUES ('Geography', 1);
SET @tid := LAST_INSERT_ID();
INSERT INTO questions (trivia_id, question_text, choice_a, choice_b, choice_c, choice_d, correct_choice) VALUES
(@tid, 'What is the capital of Canada?', 'Toronto', 'Vancouver', 'Ottawa', 'Montreal', 'C'),
(@tid, 'Which country has the city of Kyoto?', 'China', 'South Korea', 'Japan', 'Thailand', 'C'),
(@tid, 'Which ocean is on the west coast of the United States?', 'Atlantic', 'Indian', 'Arctic', 'Pacific', 'D'),
(@tid, 'Which continent is the Sahara Desert located on?', 'Asia', 'Africa', 'Australia', 'South America', 'B'),
(@tid, 'What is the longest river in South America?', 'Amazon River', 'Paraná River', 'Orinoco River', 'Magdalena River', 'A'),
(@tid, 'Which country is both in Europe and Asia?', 'Spain', 'Turkey', 'Morocco', 'Ireland', 'B'),
(@tid, 'Mount Kilimanjaro is in which country?', 'Kenya', 'Ethiopia', 'Tanzania', 'Uganda', 'C'),
(@tid, 'What is the capital of Australia?', 'Sydney', 'Melbourne', 'Canberra', 'Perth', 'C'),
(@tid, 'Which European country is famous for fjords?', 'Portugal', 'Norway', 'Greece', 'Poland', 'B'),
(@tid, 'Which sea lies between Europe and Africa?', 'Baltic Sea', 'Black Sea', 'Red Sea', 'Mediterranean Sea', 'D');

/* -------- Animals (Set 1) -------- */
INSERT INTO trivia (title, is_active) VALUES ('Animals', 1);
SET @tid := LAST_INSERT_ID();
INSERT INTO questions (trivia_id, question_text, choice_a, choice_b, choice_c, choice_d, correct_choice) VALUES
(@tid, 'Which animal is the largest land animal?', 'Giraffe', 'White Rhinoceros', 'African Elephant', 'Hippopotamus', 'C'),
(@tid, 'What is a group of lions called?', 'A pack', 'A pride', 'A herd', 'A swarm', 'B'),
(@tid, 'Which animal is the fastest land animal?', 'Cheetah', 'Pronghorn', 'Lion', 'Greyhound', 'A'),
(@tid, 'Which of these is the only mammal capable of sustained flight?', 'Flying squirrel', 'Bat', 'Sugar glider', 'Colugo', 'B'),
(@tid, 'Dolphins are:', 'Fish', 'Reptiles', 'Mammals', 'Amphibians', 'C'),
(@tid, 'How many legs does an octopus have?', '6', '8', '10', '12', 'B'),
(@tid, 'Which animal is known for eating mainly bamboo?', 'Koala', 'Giant Panda', 'Sloth', 'Red Panda', 'B'),
(@tid, 'Which bird is the largest by height?', 'Ostrich', 'Emu', 'Albatross', 'Condor', 'A'),
(@tid, 'A young kangaroo is called a:', 'Cub', 'Calf', 'Joey', 'Fawn', 'C'),
(@tid, 'Which reptile can change its skin color to blend with surroundings?', 'Iguana', 'Chameleon', 'Komodo dragon', 'Gecko', 'B');

/* -------- Top Music (Set 1) -------- */
INSERT INTO trivia (title, is_active) VALUES ('Top Music', 1);
SET @tid := LAST_INSERT_ID();
INSERT INTO questions (trivia_id, question_text, choice_a, choice_b, choice_c, choice_d, correct_choice) VALUES
(@tid, 'Who is known as the "King of Pop"?', 'Elvis Presley', 'Michael Jackson', 'Prince', 'Freddie Mercury', 'B'),
(@tid, 'Which band released "Hey Jude"?', 'The Beatles', 'The Rolling Stones', 'Queen', 'The Beach Boys', 'A'),
(@tid, 'Which instrument typically has 88 keys?', 'Harp', 'Piano', 'Accordion', 'Organ', 'B'),
(@tid, 'What does BPM stand for?', 'Beats Per Measure', 'Bars Per Minute', 'Beats Per Minute', 'Bass Pitch Measure', 'C'),
(@tid, 'Which singer released "Rolling in the Deep"?', 'Rihanna', 'Beyoncé', 'Adele', 'Taylor Swift', 'C'),
(@tid, 'Which duo is famous for performing in robot helmets?', 'The Chainsmokers', 'Daft Punk', 'Twenty One Pilots', 'OutKast', 'B'),
(@tid, 'Which composer wrote "Für Elise"?', 'Mozart', 'Bach', 'Beethoven', 'Chopin', 'C'),
(@tid, 'In common time (4/4), how many beats are in a measure?', '2', '3', '4', '6', 'C'),
(@tid, 'Which woodwind instrument uses a double reed?', 'Clarinet', 'Saxophone', 'Oboe', 'Flute', 'C'),
(@tid, 'What is the standard orchestral tuning pitch for the A above middle C?', 'A = 392 Hz', 'A = 415 Hz', 'A = 440 Hz', 'A = 460 Hz', 'C');

/* -------- 007 Bond (Brosnan, Craig, Connery) — Set 1 -------- */
INSERT INTO trivia (title, is_active) VALUES ('007', 1);
-- Fixing any stray parentheses if present:
UPDATE trivia SET title='007 Bond (Brosnan, Craig, Connery) — Set 1' WHERE title='007 Bond (Brosnan, Craig, Connery) — Set 1)';
SET @tid := LAST_INSERT_ID();
INSERT INTO questions (trivia_id, question_text, choice_a, choice_b, choice_c, choice_d, correct_choice) VALUES
(@tid, 'Who portrays James Bond in GoldenEye (1995)?', 'Pierce Brosnan', 'Daniel Craig', 'Sean Connery', 'None of these', 'A'),
(@tid, 'Which Sean Connery film features the villain Auric Goldfinger?', 'Thunderball', 'Dr. No', 'Goldfinger', 'You Only Live Twice', 'C'),
(@tid, 'Casino Royale (2006) introduced which actor as James Bond?', 'Pierce Brosnan', 'Daniel Craig', 'Sean Connery', 'None of these', 'B'),
(@tid, 'What James Bond film was released in 2012?', 'Casino Royale', 'Quantum of Solace', 'Skyfall', 'Spectre', 'C'),
(@tid, 'In The World Is Not Enough (1999), the oil heiress is named:', 'Vesper Lynd', 'Elektra King', 'Honey Ryder', 'Tatiana Romanova', 'B'),
(@tid, 'Dr. No (1962) is primarily set in:', 'Jamaica', 'Japan', 'Istanbul', 'The Bahamas', 'A'),
(@tid, 'Alec Trevelyan (006) is the main antagonist in which film?', 'Tomorrow Never Dies', 'GoldenEye', 'From Russia with Love', 'Skyfall', 'B'),
(@tid, 'Which Daniel Craig film features the cyberterrorist Raoul Silva?', 'Quantum of Solace', 'Skyfall', 'Spectre', 'Casino Royale', 'B'),
(@tid, 'Much of Diamonds Are Forever (1971) takes place in which U.S. city?', 'New York', 'Miami', 'Las Vegas', 'Los Angeles', 'C'),
(@tid, 'You Only Live Twice (1967) is largely set in:', 'Greece', 'Japan', 'Hong Kong', 'Morocco', 'B');

COMMIT;
