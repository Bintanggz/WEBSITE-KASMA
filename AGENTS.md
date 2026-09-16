# KASMA Development Rules

## Project

KASMA (Class Cash Management System) is a web-based class cash management system for university students.

The main goal is to simplify weekly class cash collection, payment verification, financial recording, and financial transparency.

## Technology Stack

- Laravel 12
- PHP 8.2+
- PostgreSQL
- Blade
- Tailwind CSS
- Alpine.js

Do not introduce React, Vue, or another frontend framework unless explicitly requested.

## Development Principles

- Follow Laravel conventions.
- Keep the architecture simple and maintainable.
- Do not overengineer the application.
- Do not introduce unnecessary dependencies.
- Do not modify unrelated files.
- Use migrations for database changes.
- Use Eloquent relationships.
- Use Form Request classes for complex validation.
- Use Policies and Middleware for authorization.
- Keep controllers thin.
- Put complex business logic into service classes when necessary.

## UI Principles

The interface must be:

- Clean
- Professional
- Modern
- Mobile-first
- Easy to use
- Comfortable for students

Avoid:

- Excessive gradients
- Glassmorphism everywhere
- Excessive animations
- Generic AI-generated dashboard designs
- Unnecessary decorative elements

Prioritize usability and information clarity.

## User Roles

The system has three roles:

1. Admin
2. Treasurer
3. Student

Authorization must be enforced on the server side.

## Financial Rules

- Only approved payments count toward class cash.
- Pending payments do not affect the balance.
- Rejected payments do not affect the balance.
- Expenses reduce the class balance.
- Duplicate payments must be prevented.
- Financial operations must preserve database consistency.
- Use database transactions when performing related financial operations.

## Payment Rules

Payment statuses:

- pending
- approved
- rejected

Students can only access their own payment records.

Treasurers can review and verify payments for their class.

Payment proof uploads must be validated and securely stored.

## Security

- Never expose secrets.
- Never hardcode credentials.
- Never trust client-side authorization.
- Validate all user input.
- Protect uploaded files.
- Use CSRF protection.
- Prevent IDOR and unauthorized access.
- Follow Laravel security conventions.
- Do not expose sensitive financial information unnecessarily.

## Database

- PostgreSQL is the primary database.
- Use Laravel migrations.
- Use foreign keys where appropriate.
- Use indexes for frequently queried fields.
- Use appropriate constraints for financial data.
- Preserve historical financial records.

## AI Agent Rules

Before implementing a major feature:

1. Analyze the existing code.
2. Explain the implementation plan.
3. Identify affected files.
4. Implement the feature.
5. Run relevant tests.
6. Check for errors.
7. Report what was changed.

Do not rewrite working code unnecessarily.

Do not install packages without explaining why they are necessary.

Do not change the database architecture without approval.

Do not implement multiple unrelated features in one task.

When a requirement is ambiguous, ask for clarification before making a major architectural decision.