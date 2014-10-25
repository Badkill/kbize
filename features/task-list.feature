Feature: task:list
    In order to view the task list
    I need to ...
    Then ...

    Scenario: Obtains list of tasks
        Given I am an authenticated user
        And I want to launch "task:list" command
        And I insert "2" as input
        # should be implemented
        And the story "Story Title" of "john" exists
        When command is executed
        Then the output contains "ID.*Assignee.*Title"
        Then the output contains "Task Title"
        Then the output contains "john"

    Scenario: Obtains a list of a specific users tasks
        Given I am an authenticated user
        # should be implemented
        And the story "Story Title" of "john" exists
        And the story "Merchant API" of "name.surname" exists
        And I want to launch "task:list" command
        And I use the "filters" argument "@name.surname"
        And I insert "2" as input
        When command is executed
        Then the output contains "name.surname"
        Then the output does not contains "john"

    Scenario: Obtains a list with a specific title
        Given I am an authenticated user
        # should be implemented
        And the story "Story Title" of "john" exists
        And the story "Merchant API" of "name.surname" exists
        And I want to launch "task:list" command
        And I use the "filters" argument "API"
        And I insert "2" as input
        When command is executed
        Then the output contains "API"
        Then the output does not contains "Story Title"

    Scenario: Show a story
        Given I am an authenticated user
        # should be implemented
        And the story "Merchant API" of "name.surname" exists
        And I want to launch "task:list" command
        And I use the "show" option
        And I use the "board" option with value "2"
        And I use the "filters" argument "Merchant API"
        When command is executed
        Then the output contains "Merchant API"
        Then the output contains "description"
        Then the output contains "type"
        # Then show the output
