-- Migration: add featured-image support to an EXISTING blog_app database
-- Run this once if your database was created before the image feature was added.
-- (Skip this if you're setting up the database fresh from schema.sql - it already has the column.)

ALTER TABLE blogPost
    ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER content;
