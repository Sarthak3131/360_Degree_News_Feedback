-- Add new rating columns
ALTER TABLE feedback
ADD COLUMN impact_rating INT NOT NULL CHECK (impact_rating BETWEEN 1 AND 5) AFTER news_title,
ADD COLUMN accuracy_rating INT NOT NULL CHECK (accuracy_rating BETWEEN 1 AND 5) AFTER impact_rating,
ADD COLUMN clarity_rating INT NOT NULL CHECK (clarity_rating BETWEEN 1 AND 5) AFTER accuracy_rating,
ADD COLUMN suggestions TEXT NOT NULL AFTER feedback;

-- Drop old source and rating columns if they exist
ALTER TABLE feedback
DROP COLUMN IF EXISTS source,
DROP COLUMN IF EXISTS rating;
