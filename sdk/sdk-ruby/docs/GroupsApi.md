# DirectusSDK::GroupsApi

All URIs are relative to *https://myinstance.directus.io/api/1.1*

Method | HTTP request | Description
------------- | ------------- | -------------
[**add_group**](GroupsApi.md#add_group) | **POST** /groups | Add a new group
[**add_privilege**](GroupsApi.md#add_privilege) | **POST** /privileges/{groupId} | Create new table privileges for the specified user group
[**get_group**](GroupsApi.md#get_group) | **GET** /groups/{groupId} | Returns specific group
[**get_groups**](GroupsApi.md#get_groups) | **GET** /groups | Returns groups
[**get_privileges**](GroupsApi.md#get_privileges) | **GET** /privileges/{groupId} | Returns group privileges
[**get_privileges_for_table**](GroupsApi.md#get_privileges_for_table) | **GET** /privileges/{groupId}/{tableNameOrPrivilegeId} | Returns group privileges by tableName
[**update_privileges**](GroupsApi.md#update_privileges) | **PUT** /privileges/{groupId}/{tableNameOrPrivilegeId} | Update privileges by privilegeId


# **add_group**
> add_group(opts)

Add a new group

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::GroupsApi.new

opts = { 
  name: "name_example" # String | Name of group to add
}

begin
  #Add a new group
  api_instance.add_group(opts)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling GroupsApi->add_group: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **String**| Name of group to add | [optional] 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



# **add_privilege**
> add_privilege(group_id, , opts)

Create new table privileges for the specified user group

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::GroupsApi.new

group_id = "group_id_example" # String | ID of group to return

opts = { 
  id: 56, # Integer | Privilege's Unique Identification number
  table_name: "table_name_example", # String | Name of table to add
  allow_add: 56, # Integer | Permission to add/create entries in the table (See values below)
  allow_edit: 56, # Integer | Permission to edit/update entries in the table (See values below)
  allow_delete: 56, # Integer | Permission to delete/remove entries in the table (See values below)
  allow_view: 56, # Integer | Permission to view/read entries in the table (See values below)
  allow_alter: 56, # Integer | Permission to add/create entries in the table (See values below)
  nav_listed: true, # BOOLEAN | If the table should be visible in the sidebar for this user group
  read_field_blacklist: "read_field_blacklist_example", # String | A CSV of column names that the group can't view (read)
  write_field_blacklist: "write_field_blacklist_example", # String | A CSV of column names that the group can't edit (update)
  status_id: "status_id_example" # String | State of the record that this permissions belongs to (Draft, Active or Soft Deleted)
}

begin
  #Create new table privileges for the specified user group
  api_instance.add_privilege(group_id, , opts)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling GroupsApi->add_privilege: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **group_id** | **String**| ID of group to return | 
 **id** | **Integer**| Privilege&#39;s Unique Identification number | [optional] 
 **table_name** | **String**| Name of table to add | [optional] 
 **allow_add** | **Integer**| Permission to add/create entries in the table (See values below) | [optional] 
 **allow_edit** | **Integer**| Permission to edit/update entries in the table (See values below) | [optional] 
 **allow_delete** | **Integer**| Permission to delete/remove entries in the table (See values below) | [optional] 
 **allow_view** | **Integer**| Permission to view/read entries in the table (See values below) | [optional] 
 **allow_alter** | **Integer**| Permission to add/create entries in the table (See values below) | [optional] 
 **nav_listed** | **BOOLEAN**| If the table should be visible in the sidebar for this user group | [optional] 
 **read_field_blacklist** | **String**| A CSV of column names that the group can&#39;t view (read) | [optional] 
 **write_field_blacklist** | **String**| A CSV of column names that the group can&#39;t edit (update) | [optional] 
 **status_id** | **String**| State of the record that this permissions belongs to (Draft, Active or Soft Deleted) | [optional] 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



# **get_group**
> GetGroup get_group(group_id, )

Returns specific group

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::GroupsApi.new

group_id = "group_id_example" # String | ID of group to return


begin
  #Returns specific group
  result = api_instance.get_group(group_id, )
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling GroupsApi->get_group: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **group_id** | **String**| ID of group to return | 

### Return type

[**GetGroup**](GetGroup.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_groups**
> GetGroups get_groups

Returns groups

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::GroupsApi.new

begin
  #Returns groups
  result = api_instance.get_groups
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling GroupsApi->get_groups: #{e}"
end
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetGroups**](GetGroups.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_privileges**
> GetPrivileges get_privileges(group_id, )

Returns group privileges

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::GroupsApi.new

group_id = "group_id_example" # String | ID of group to return


begin
  #Returns group privileges
  result = api_instance.get_privileges(group_id, )
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling GroupsApi->get_privileges: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **group_id** | **String**| ID of group to return | 

### Return type

[**GetPrivileges**](GetPrivileges.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **get_privileges_for_table**
> GetPrivilegesForTable get_privileges_for_table(group_id, table_name_or_privilege_id)

Returns group privileges by tableName

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::GroupsApi.new

group_id = "group_id_example" # String | ID of group to return

table_name_or_privilege_id = "table_name_or_privilege_id_example" # String | ID of privileges or Table Name to use


begin
  #Returns group privileges by tableName
  result = api_instance.get_privileges_for_table(group_id, table_name_or_privilege_id)
  p result
rescue DirectusSDK::ApiError => e
  puts "Exception when calling GroupsApi->get_privileges_for_table: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **group_id** | **String**| ID of group to return | 
 **table_name_or_privilege_id** | **String**| ID of privileges or Table Name to use | 

### Return type

[**GetPrivilegesForTable**](GetPrivilegesForTable.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json



# **update_privileges**
> update_privileges(group_id, table_name_or_privilege_id, opts)

Update privileges by privilegeId

### Example
```ruby
# load the gem
require 'directus_sdk'
# setup authorization
DirectusSDK.configure do |config|
  # Configure API key authorization: api_key
  config.api_key['access_token'] = 'YOUR API KEY'
  # Uncomment the following line to set a prefix for the API key, e.g. 'Bearer' (defaults to nil)
  #config.api_key_prefix['access_token'] = 'Bearer'
  # Configure your host
  config.host = "myinstance.directus.io"
  # Enable or disable debugging output
  config.debugging = false
end

api_instance = DirectusSDK::GroupsApi.new

group_id = "group_id_example" # String | ID of group to return

table_name_or_privilege_id = "table_name_or_privilege_id_example" # String | ID of privileges or Table Name to use

opts = { 
  privileges_id: "privileges_id_example", # String | ubique privilege ID
  group_id2: "group_id_example", # String | ID of group to return
  table_name: "table_name_example", # String | Name of table to add
  allow_add: 56, # Integer | Permission to add/create entries in the table (See values below)
  allow_edit: 56, # Integer | Permission to edit/update entries in the table (See values below)
  allow_delete: 56, # Integer | Permission to delete/remove entries in the table (See values below)
  allow_view: 56, # Integer | Permission to view/read entries in the table (See values below)
  allow_alter: 56, # Integer | Permission to add/create entries in the table (See values below)
  nav_listed: true, # BOOLEAN | If the table should be visible in the sidebar for this user group
  read_field_blacklist: "read_field_blacklist_example", # String | A CSV of column names that the group can't view (read)
  write_field_blacklist: "write_field_blacklist_example", # String | A CSV of column names that the group can't edit (update)
  status_id: "status_id_example" # String | State of the record that this permissions belongs to (Draft, Active or Soft Deleted)
}

begin
  #Update privileges by privilegeId
  api_instance.update_privileges(group_id, table_name_or_privilege_id, opts)
rescue DirectusSDK::ApiError => e
  puts "Exception when calling GroupsApi->update_privileges: #{e}"
end
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **group_id** | **String**| ID of group to return | 
 **table_name_or_privilege_id** | **String**| ID of privileges or Table Name to use | 
 **privileges_id** | **String**| ubique privilege ID | [optional] 
 **group_id2** | **String**| ID of group to return | [optional] 
 **table_name** | **String**| Name of table to add | [optional] 
 **allow_add** | **Integer**| Permission to add/create entries in the table (See values below) | [optional] 
 **allow_edit** | **Integer**| Permission to edit/update entries in the table (See values below) | [optional] 
 **allow_delete** | **Integer**| Permission to delete/remove entries in the table (See values below) | [optional] 
 **allow_view** | **Integer**| Permission to view/read entries in the table (See values below) | [optional] 
 **allow_alter** | **Integer**| Permission to add/create entries in the table (See values below) | [optional] 
 **nav_listed** | **BOOLEAN**| If the table should be visible in the sidebar for this user group | [optional] 
 **read_field_blacklist** | **String**| A CSV of column names that the group can&#39;t view (read) | [optional] 
 **write_field_blacklist** | **String**| A CSV of column names that the group can&#39;t edit (update) | [optional] 
 **status_id** | **String**| State of the record that this permissions belongs to (Draft, Active or Soft Deleted) | [optional] 

### Return type

nil (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json



