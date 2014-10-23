Feature: Login
    In order to use kanbanize api
    I need to be authenticated

    Scenario: Successfull authentication with email and password
        Given I am an unauthenticated user
        When I want to view projects list
        And I write my email: "name.surname@email.com"
        And I write my password: "secret"
        Then command is executed
        Then my token is stored

    Scenario: User and password are not request with an already authenticated user
        Given I am an authenticated user
        When I want to view projects list
        Then command is executed

    Scenario: User and password are requested with an already authenticated user with wrong token
        Given I am an authenticated user
        And I have an expired token
        When I want to view projects list
        And I write my email: "name.surname@email.com"
        And I write my password: "secret"
        Then command is executed
        Then my token is stored

