# EasySwagger

## Playbook

### 1 - Create Basic HTTP interface. End user will provide URI, method, params, body. They will be required to know how to format each, but can quickly run any API call without any checks. Basically a raw interface to not have to deal with setting up HTTP, authentication, etc. Will return both the body, headers, and status

### 2 - Add a layer where end user provides a path and an array/object of data. Class will determine whether a piece of data belongs in the path, URI params, or body. 

### 3 - Start adding in other swagger data instead of just my personal immediate needs for a specific API being worked on personally

!!!! Currently experimental version for testing purposes. 