DROP TRIGGER IF EXISTS adoption_requests_set_updated_at ON adoption_requests;
DROP TRIGGER IF EXISTS pets_set_updated_at ON pets;
DROP TRIGGER IF EXISTS user_verifications_set_updated_at ON user_verifications;
DROP TRIGGER IF EXISTS users_set_updated_at ON users;
DROP TRIGGER IF EXISTS organizations_set_updated_at ON organizations;

DROP TABLE IF EXISTS adoption_requests;
DROP TABLE IF EXISTS matches;
DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS swipes;
DROP TABLE IF EXISTS pet_photos;
DROP TABLE IF EXISTS pets;
DROP TABLE IF EXISTS user_verifications;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS organizations;
DROP FUNCTION IF EXISTS set_updated_at();
