Feature: Login
    In order to use kanbanize api
    I need to be authenticated

    Scenario: Successfull authentication with email and password
        Given I am an unauthenticated user
        And I want to launch "task:list" command
        And I insert my email "name.surname@email.com" as input
        And I insert my password "secret" as input
        And I insert "2" as input
        When command is executed
        Then my token is stored

    Scenario: User and password are not requested for an already authenticated user
        Given I am an authenticated user
        And I want to launch "task:list" command
        And I insert "2" as input
        When command is executed
        Then no more inputs are requested

    Scenario: User and password are requested with an already authenticated user with wrong token
        Given I am an authenticated user
        And I have an expired token
        And I want to launch "task:list" command
        And I insert my email "name.surname@email.com" as input
        And I insert my password "secret" as input
        And I insert "2" as input
        When command is executed
        Then my token is stored
