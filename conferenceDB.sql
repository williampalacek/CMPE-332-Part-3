-- ============================================================
--  conferenceDB.sql
--  Conference Database -- CMPE 332 Project Part 2
-- ============================================================

DROP DATABASE IF EXISTS conferenceDB;
CREATE DATABASE conferenceDB;
USE conferenceDB;

-- ------------------------------------------------------------
-- ORGANIZING COMMITTEE
-- ------------------------------------------------------------

CREATE TABLE SubCommittee (
    committeeID   INT AUTO_INCREMENT,
    committeeName VARCHAR(100) NOT NULL,
    PRIMARY KEY (committeeID)
);

CREATE TABLE CommitteeMember (
    memberID  INT AUTO_INCREMENT,
    firstName VARCHAR(50)  NOT NULL,
    lastName  VARCHAR(50)  NOT NULL,
    email     VARCHAR(100) NOT NULL,
    PRIMARY KEY (memberID)
);

-- M:N between CommitteeMember and SubCommittee; isChair marks the chair
CREATE TABLE Membership (
    memberID    INT     NOT NULL,
    committeeID INT     NOT NULL,
    isChair     BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (memberID, committeeID),
    FOREIGN KEY (memberID)    REFERENCES CommitteeMember(memberID)  ON DELETE CASCADE,
    FOREIGN KEY (committeeID) REFERENCES SubCommittee(committeeID)  ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- HOTEL
-- ------------------------------------------------------------

CREATE TABLE HotelRoom (
    roomNumber VARCHAR(10) NOT NULL,
    numBeds    INT         NOT NULL,
    PRIMARY KEY (roomNumber)
);

-- ------------------------------------------------------------
-- ATTENDEES  (supertype + subtypes)
-- ------------------------------------------------------------

CREATE TABLE Attendee (
    attendeeID   INT AUTO_INCREMENT,
    firstName    VARCHAR(50)  NOT NULL,
    lastName     VARCHAR(50)  NOT NULL,
    email        VARCHAR(100),
    attendeeType ENUM('student','professional','sponsor') NOT NULL,
    PRIMARY KEY (attendeeID)
);

-- Students may optionally be assigned a hotel room
CREATE TABLE Student (
    attendeeID INT,
    roomNumber VARCHAR(10),
    PRIMARY KEY (attendeeID),
    FOREIGN KEY (attendeeID) REFERENCES Attendee(attendeeID)  ON DELETE CASCADE,
    FOREIGN KEY (roomNumber) REFERENCES HotelRoom(roomNumber) ON DELETE SET NULL
);

CREATE TABLE Professional (
    attendeeID   INT,
    organization VARCHAR(100),
    PRIMARY KEY (attendeeID),
    FOREIGN KEY (attendeeID) REFERENCES Attendee(attendeeID) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- SPONSORS
-- ------------------------------------------------------------

CREATE TABLE SponsoringCompany (
    companyID    INT AUTO_INCREMENT,
    companyName  VARCHAR(100) NOT NULL,
    sponsorLevel ENUM('Platinum','Gold','Silver','Bronze') NOT NULL,
    emailsSent   INT          NOT NULL DEFAULT 0,
    PRIMARY KEY (companyID)
);

-- Sponsor attendees represent a sponsoring company
CREATE TABLE SponsorAttendee (
    attendeeID INT NOT NULL,
    companyID  INT NOT NULL,
    PRIMARY KEY (attendeeID),
    FOREIGN KEY (attendeeID) REFERENCES Attendee(attendeeID)         ON DELETE CASCADE,
    FOREIGN KEY (companyID)  REFERENCES SponsoringCompany(companyID) ON DELETE CASCADE
);

CREATE TABLE JobAd (
    jobID     INT AUTO_INCREMENT,
    title     VARCHAR(100)   NOT NULL,
    city      VARCHAR(50)    NOT NULL,
    province  VARCHAR(50)    NOT NULL,
    payRate   DECIMAL(10,2)  NOT NULL,
    companyID INT            NOT NULL,
    PRIMARY KEY (jobID),
    FOREIGN KEY (companyID) REFERENCES SponsoringCompany(companyID) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- SESSIONS  (conference schedule)
-- ------------------------------------------------------------

CREATE TABLE Session (
    sessionID    INT AUTO_INCREMENT,
    sessionName  VARCHAR(100) NOT NULL,
    sessionDay   ENUM('Day 1','Day 2') NOT NULL,
    startTime    TIME         NOT NULL,
    endTime      TIME         NOT NULL,
    roomLocation VARCHAR(50)  NOT NULL,
    PRIMARY KEY (sessionID)
);

-- M:N between Attendee and Session (speakers must be attendees)
CREATE TABLE Speaks (
    attendeeID INT NOT NULL,
    sessionID  INT NOT NULL,
    PRIMARY KEY (attendeeID, sessionID),
    FOREIGN KEY (attendeeID) REFERENCES Attendee(attendeeID) ON DELETE CASCADE,
    FOREIGN KEY (sessionID)  REFERENCES Session(sessionID)   ON DELETE CASCADE
);


-- ============================================================
--  DATA
-- ============================================================

-- Sub-committees
INSERT INTO SubCommittee (committeeName) VALUES
    ('Program Committee'),
    ('Registration Committee'),
    ('Sponsorship Committee'),
    ('Logistics Committee'),
    ('Social Committee'),
    ('Finance Committee');

-- Committee members
INSERT INTO CommitteeMember (firstName, lastName, email) VALUES
    ('Alice',  'Johnson', 'alice.johnson@conf.org'),
    ('Bob',    'Smith',   'bob.smith@conf.org'),
    ('Carol',  'White',   'carol.white@conf.org'),
    ('David',  'Brown',   'david.brown@conf.org'),
    ('Emma',   'Davis',   'emma.davis@conf.org'),
    ('Frank',  'Wilson',  'frank.wilson@conf.org'),
    ('Grace',  'Lee',     'grace.lee@conf.org'),
    ('Henry',  'Taylor',  'henry.taylor@conf.org');

-- Membership (memberID, committeeID, isChair)
INSERT INTO Membership (memberID, committeeID, isChair) VALUES
    (1, 1, TRUE),   -- Alice  chairs Program
    (2, 1, FALSE),  -- Bob    on Program
    (3, 1, FALSE),  -- Carol  on Program
    (4, 2, TRUE),   -- David  chairs Registration
    (5, 2, FALSE),  -- Emma   on Registration
    (3, 2, FALSE),  -- Carol  on Registration
    (6, 3, TRUE),   -- Frank  chairs Sponsorship
    (7, 3, FALSE),  -- Grace  on Sponsorship
    (2, 3, FALSE),  -- Bob    on Sponsorship
    (8, 4, TRUE),   -- Henry  chairs Logistics
    (1, 4, FALSE),  -- Alice  on Logistics
    (5, 4, FALSE),  -- Emma   on Logistics
    (2, 5, TRUE),   -- Bob    chairs Social
    (3, 5, FALSE),  -- Carol  on Social
    (4, 5, FALSE),  -- David  on Social
    (5, 6, TRUE),   -- Emma   chairs Finance
    (6, 6, FALSE),  -- Frank  on Finance
    (7, 6, FALSE);  -- Grace  on Finance

-- Hotel rooms
INSERT INTO HotelRoom (roomNumber, numBeds) VALUES
    ('101', 2),
    ('102', 2),
    ('103', 1),
    ('104', 2),
    ('105', 1),
    ('106', 2),
    ('107', 2),
    ('108', 1);

-- Attendees (students 1-8, professionals 9-16, sponsors 17-24)
INSERT INTO Attendee (firstName, lastName, email, attendeeType) VALUES
    -- Students
    ('Tom',       'Anderson',  'tom.anderson@university.ca',    'student'),
    ('Sara',      'Mitchell',  'sara.mitchell@university.ca',   'student'),
    ('Jake',      'Thompson',  'jake.t@university.ca',          'student'),
    ('Lily',      'Chen',      'lily.chen@university.ca',       'student'),
    ('Chris',     'Parker',    'chris.parker@college.ca',       'student'),
    ('Mia',       'Scott',     'mia.scott@college.ca',          'student'),
    ('Noah',      'Harris',    'noah.harris@university.ca',     'student'),
    ('Ava',       'Martinez',  'ava.martinez@college.ca',       'student'),
    -- Professionals
    ('John',      'Evans',     'j.evans@techuniversity.ca',     'professional'),
    ('Mary',      'Clark',     'm.clark@medresearch.ca',        'professional'),
    ('Robert',    'Hall',      'r.hall@datalab.ca',             'professional'),
    ('Patricia',  'Young',     'p.young@webdev.ca',             'professional'),
    ('James',     'Wright',    'j.wright@airesearch.ca',        'professional'),
    ('Linda',     'King',      'l.king@cybersec.ca',            'professional'),
    ('Charles',   'Scott',     'c.scott@cloudsystems.ca',       'professional'),
    ('Barbara',   'Hill',      'b.hill@softwaredev.ca',         'professional'),
    -- Sponsors
    ('Michael',   'Chen',      'mchen@techcorp.com',            'sponsor'),
    ('Jennifer',  'Lee',       'jlee@techcorp.com',             'sponsor'),
    ('Daniel',    'Kim',       'dkim@innovatesoft.com',         'sponsor'),
    ('Rachel',    'Brown',     'rbrown@datasystems.com',        'sponsor'),
    ('Kevin',     'Williams',  'kwilliams@cloudnet.com',        'sponsor'),
    ('Stephanie', 'Davis',     'sdavis@cybersec.com',           'sponsor'),
    ('Ryan',      'Taylor',    'rtaylor@ailabs.com',            'sponsor'),
    ('Amanda',    'White',     'awhite@webworks.com',           'sponsor');

-- Students -> hotel rooms (Ava has no room assigned)
INSERT INTO Student (attendeeID, roomNumber) VALUES
    (1,  '101'),
    (2,  '101'),
    (3,  '102'),
    (4,  '102'),
    (5,  '103'),
    (6,  '104'),
    (7,  '104'),
    (8,  NULL);

-- Professionals -> organizations
INSERT INTO Professional (attendeeID, organization) VALUES
    (9,  'Tech University'),
    (10, 'Medical Research Institute'),
    (11, 'Data Lab Inc'),
    (12, 'Web Dev Solutions'),
    (13, 'AI Research Centre'),
    (14, 'CyberSec Professionals'),
    (15, 'Cloud Systems Ltd'),
    (16, 'Software Dev Corp');

-- Sponsoring companies
INSERT INTO SponsoringCompany (companyName, sponsorLevel, emailsSent) VALUES
    ('TechCorp',          'Platinum', 3),
    ('InnovateSoft',      'Gold',     2),
    ('DataSystems',       'Silver',   1),
    ('CloudNet',          'Bronze',   0),
    ('CyberSec Solutions','Gold',     1),
    ('DevTools Inc',      'Silver',   0),
    ('AILabs',            'Platinum', 4),
    ('WebWorks',          'Bronze',   0);

-- Sponsor attendees -> companies
INSERT INTO SponsorAttendee (attendeeID, companyID) VALUES
    (17, 1),   -- Michael Chen    -> TechCorp
    (18, 1),   -- Jennifer Lee    -> TechCorp
    (19, 2),   -- Daniel Kim      -> InnovateSoft
    (20, 3),   -- Rachel Brown    -> DataSystems
    (21, 4),   -- Kevin Williams  -> CloudNet
    (22, 5),   -- Stephanie Davis -> CyberSec Solutions
    (23, 7),   -- Ryan Taylor     -> AILabs
    (24, 8);   -- Amanda White    -> WebWorks

-- Job ads
INSERT INTO JobAd (title, city, province, payRate, companyID) VALUES
    ('Software Engineer',         'Toronto',   'Ontario',          90000.00, 1),
    ('Data Analyst',              'Vancouver', 'British Columbia',  75000.00, 1),
    ('Product Manager',           'Toronto',   'Ontario',          110000.00, 2),
    ('Cloud Architect',           'Calgary',   'Alberta',          120000.00, 3),
    ('Security Analyst',          'Ottawa',    'Ontario',           85000.00, 5),
    ('DevOps Engineer',           'Montreal',  'Quebec',            95000.00, 6),
    ('Machine Learning Engineer', 'Toronto',   'Ontario',          115000.00, 7),
    ('Full Stack Developer',      'Vancouver', 'British Columbia',  80000.00, 8);

-- Sessions
INSERT INTO Session (sessionName, sessionDay, startTime, endTime, roomLocation) VALUES
    ('Opening Keynote',             'Day 1', '09:00:00', '10:00:00', 'Main Hall'),
    ('AI in Healthcare',            'Day 1', '10:30:00', '11:30:00', 'Room A'),
    ('Cloud Security Trends',       'Day 1', '10:30:00', '11:30:00', 'Room B'),
    ('Database Design Patterns',    'Day 1', '14:00:00', '15:00:00', 'Room A'),
    ('Web Development Trends',      'Day 1', '14:00:00', '15:00:00', 'Room B'),
    ('Machine Learning Workshop',   'Day 2', '09:00:00', '10:30:00', 'Main Hall'),
    ('Cybersecurity Best Practices','Day 2', '11:00:00', '12:00:00', 'Room A'),
    ('Closing Keynote',             'Day 2', '14:00:00', '15:00:00', 'Main Hall');

-- Speakers (must be attendees)
INSERT INTO Speaks (attendeeID, sessionID) VALUES
    (9,  1),   -- John Evans      -> Opening Keynote
    (10, 2),   -- Mary Clark      -> AI in Healthcare
    (22, 3),   -- Stephanie Davis -> Cloud Security Trends
    (11, 4),   -- Robert Hall     -> Database Design Patterns
    (12, 5),   -- Patricia Young  -> Web Development Trends
    (23, 6),   -- Ryan Taylor     -> Machine Learning Workshop
    (14, 7),   -- Linda King      -> Cybersecurity Best Practices
    (9,  8),   -- John Evans      -> Closing Keynote
    (13, 8);   -- James Wright    -> Closing Keynote
