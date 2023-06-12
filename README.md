# Chat Api with Slim Framework

This application includes APIs where users can create groups, join groups, and send messages to groups.

<hr><br>

## Install the Application

<br>

First of all You will require `PHP` 7.4 or newer and `composer`.

After copying the project, you can start it by running the following commands in the terminal, in order:

```bash
composer update
```

```bash
composer start
```

The application will start on port 8080 of your computer.
<br><hr><br>

## Initilize the database

<br>
Send request to localhost:8080/ to create database structer.
<br><br>
We are using SQLite as our database. We have four tables:

- users => This table store the users data.

* - username: Name of the user.
* - token: To authenticate a user, an automatically generated identifier is used. It is required to create a group, join a group, send messages to a group, view messages in a group, and view group members.
    <br>

- chat_groups => This table strore the created groups.

* - name: Name of the group.

- user_groups => This table takes the group and user information as foreign keys to provide access to users in the groups.

* - user_id: user id.
* - group_id: group id.
    <br>

- messages => This table store the messages sent to the groups.

* - user_id: user id.
* - group_id: group id.
* - sender: name of sender.
* - message: the message.
* - created_at: Message send time.
  <hr><br>

## Endpoints

### User <br>

- ### Post /users. {This API creates a new user.}
- - name (string) <br><br>

- ### Get /users. {This API returns all user data.}<br>

- ### Get /users/{id}. {This API returns spesific user data.}
<br>
<hr>

### Group <br>

- ### Post /groups. {This API creates a new group.}
- - name (string) <br><br>

- ### Get /groups. {This API returns all group data.}<br>

- ### Get /groups/{id}/users. {This API returns names of group members.}
<br>
<hr>

### Message <br>

- ### Post /messages/{group_id}. {This API sends message to group.}
- - message (string) <br><br>

- ### Get /messages/{group_id}. {This API returns all messages from group.}<br>

<hr>
