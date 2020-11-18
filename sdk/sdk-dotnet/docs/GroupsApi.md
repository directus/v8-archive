# IO.Directus.Api.GroupsApi

All URIs are relative to your configured base path, as per example.

Method | HTTP request | Description
------------- | ------------- | -------------
[**AddGroup**](GroupsApi.md#addgroup) | **POST** /groups | Add a new group
[**AddPrivilege**](GroupsApi.md#addprivilege) | **POST** /privileges/{groupId} | Create new table privileges for the specified user group
[**GetGroup**](GroupsApi.md#getgroup) | **GET** /groups/{groupId} | Returns specific group
[**GetGroups**](GroupsApi.md#getgroups) | **GET** /groups | Returns groups
[**GetPrivileges**](GroupsApi.md#getprivileges) | **GET** /privileges/{groupId} | Returns group privileges
[**GetPrivilegesForTable**](GroupsApi.md#getprivilegesfortable) | **GET** /privileges/{groupId}/{tableNameOrPrivilegeId} | Returns group privileges by tableName
[**UpdatePrivileges**](GroupsApi.md#updateprivileges) | **PUT** /privileges/{groupId}/{tableNameOrPrivilegeId} | Update privileges by privilegeId


<a name="addgroup"></a>
# **AddGroup**
> void AddGroup (string name = null)

Add a new group

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class AddGroupExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new GroupsApi();
            var name = name_example;  // string | Name of group to add (optional) 

            try
            {
                // Add a new group
                apiInstance.AddGroup(name);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling GroupsApi.AddGroup: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **name** | **string**| Name of group to add | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="addprivilege"></a>
# **AddPrivilege**
> void AddPrivilege (string groupId, int? id = null, string tableName = null, int? allowAdd = null, int? allowEdit = null, int? allowDelete = null, int? allowView = null, int? allowAlter = null, bool? navListed = null, string readFieldBlacklist = null, string writeFieldBlacklist = null, string statusId = null)

Create new table privileges for the specified user group

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class AddPrivilegeExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new GroupsApi();
            var groupId = groupId_example;  // string | ID of group to return
            var id = 56;  // int? | Privilege's Unique Identification number (optional) 
            var tableName = tableName_example;  // string | Name of table to add (optional) 
            var allowAdd = 56;  // int? | Permission to add/create entries in the table (See values below) (optional) 
            var allowEdit = 56;  // int? | Permission to edit/update entries in the table (See values below) (optional) 
            var allowDelete = 56;  // int? | Permission to delete/remove entries in the table (See values below) (optional) 
            var allowView = 56;  // int? | Permission to view/read entries in the table (See values below) (optional) 
            var allowAlter = 56;  // int? | Permission to add/create entries in the table (See values below) (optional) 
            var navListed = true;  // bool? | If the table should be visible in the sidebar for this user group (optional) 
            var readFieldBlacklist = readFieldBlacklist_example;  // string | A CSV of column names that the group can't view (read) (optional) 
            var writeFieldBlacklist = writeFieldBlacklist_example;  // string | A CSV of column names that the group can't edit (update) (optional) 
            var statusId = statusId_example;  // string | State of the record that this permissions belongs to (Draft, Active or Soft Deleted) (optional) 

            try
            {
                // Create new table privileges for the specified user group
                apiInstance.AddPrivilege(groupId, id, tableName, allowAdd, allowEdit, allowDelete, allowView, allowAlter, navListed, readFieldBlacklist, writeFieldBlacklist, statusId);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling GroupsApi.AddPrivilege: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **string**| ID of group to return | 
 **id** | **int?**| Privilege&#39;s Unique Identification number | [optional] 
 **tableName** | **string**| Name of table to add | [optional] 
 **allowAdd** | **int?**| Permission to add/create entries in the table (See values below) | [optional] 
 **allowEdit** | **int?**| Permission to edit/update entries in the table (See values below) | [optional] 
 **allowDelete** | **int?**| Permission to delete/remove entries in the table (See values below) | [optional] 
 **allowView** | **int?**| Permission to view/read entries in the table (See values below) | [optional] 
 **allowAlter** | **int?**| Permission to add/create entries in the table (See values below) | [optional] 
 **navListed** | **bool?**| If the table should be visible in the sidebar for this user group | [optional] 
 **readFieldBlacklist** | **string**| A CSV of column names that the group can&#39;t view (read) | [optional] 
 **writeFieldBlacklist** | **string**| A CSV of column names that the group can&#39;t edit (update) | [optional] 
 **statusId** | **string**| State of the record that this permissions belongs to (Draft, Active or Soft Deleted) | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="getgroup"></a>
# **GetGroup**
> GetGroup GetGroup (string groupId)

Returns specific group

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetGroupExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new GroupsApi();
            var groupId = groupId_example;  // string | ID of group to return

            try
            {
                // Returns specific group
                GetGroup result = apiInstance.GetGroup(groupId);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling GroupsApi.GetGroup: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **string**| ID of group to return | 

### Return type

[**GetGroup**](GetGroup.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="getgroups"></a>
# **GetGroups**
> GetGroups GetGroups ()

Returns groups

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetGroupsExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new GroupsApi();

            try
            {
                // Returns groups
                GetGroups result = apiInstance.GetGroups();
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling GroupsApi.GetGroups: " + e.Message );
            }
        }
    }
}
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

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="getprivileges"></a>
# **GetPrivileges**
> GetPrivileges GetPrivileges (string groupId)

Returns group privileges

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetPrivilegesExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new GroupsApi();
            var groupId = groupId_example;  // string | ID of group to return

            try
            {
                // Returns group privileges
                GetPrivileges result = apiInstance.GetPrivileges(groupId);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling GroupsApi.GetPrivileges: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **string**| ID of group to return | 

### Return type

[**GetPrivileges**](GetPrivileges.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="getprivilegesfortable"></a>
# **GetPrivilegesForTable**
> GetPrivilegesForTable GetPrivilegesForTable (string groupId, string tableNameOrPrivilegeId)

Returns group privileges by tableName

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetPrivilegesForTableExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new GroupsApi();
            var groupId = groupId_example;  // string | ID of group to return
            var tableNameOrPrivilegeId = tableNameOrPrivilegeId_example;  // string | ID of privileges or Table Name to use

            try
            {
                // Returns group privileges by tableName
                GetPrivilegesForTable result = apiInstance.GetPrivilegesForTable(groupId, tableNameOrPrivilegeId);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling GroupsApi.GetPrivilegesForTable: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **string**| ID of group to return | 
 **tableNameOrPrivilegeId** | **string**| ID of privileges or Table Name to use | 

### Return type

[**GetPrivilegesForTable**](GetPrivilegesForTable.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="updateprivileges"></a>
# **UpdatePrivileges**
> void UpdatePrivileges (string groupId, string tableNameOrPrivilegeId, string privilegesId = null, string groupId2 = null, string tableName = null, int? allowAdd = null, int? allowEdit = null, int? allowDelete = null, int? allowView = null, int? allowAlter = null, bool? navListed = null, string readFieldBlacklist = null, string writeFieldBlacklist = null, string statusId = null)

Update privileges by privilegeId

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class UpdatePrivilegesExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new GroupsApi();
            var groupId = groupId_example;  // string | ID of group to return
            var tableNameOrPrivilegeId = tableNameOrPrivilegeId_example;  // string | ID of privileges or Table Name to use
            var privilegesId = privilegesId_example;  // string | ubique privilege ID (optional) 
            var groupId2 = groupId_example;  // string | ID of group to return (optional) 
            var tableName = tableName_example;  // string | Name of table to add (optional) 
            var allowAdd = 56;  // int? | Permission to add/create entries in the table (See values below) (optional) 
            var allowEdit = 56;  // int? | Permission to edit/update entries in the table (See values below) (optional) 
            var allowDelete = 56;  // int? | Permission to delete/remove entries in the table (See values below) (optional) 
            var allowView = 56;  // int? | Permission to view/read entries in the table (See values below) (optional) 
            var allowAlter = 56;  // int? | Permission to add/create entries in the table (See values below) (optional) 
            var navListed = true;  // bool? | If the table should be visible in the sidebar for this user group (optional) 
            var readFieldBlacklist = readFieldBlacklist_example;  // string | A CSV of column names that the group can't view (read) (optional) 
            var writeFieldBlacklist = writeFieldBlacklist_example;  // string | A CSV of column names that the group can't edit (update) (optional) 
            var statusId = statusId_example;  // string | State of the record that this permissions belongs to (Draft, Active or Soft Deleted) (optional) 

            try
            {
                // Update privileges by privilegeId
                apiInstance.UpdatePrivileges(groupId, tableNameOrPrivilegeId, privilegesId, groupId2, tableName, allowAdd, allowEdit, allowDelete, allowView, allowAlter, navListed, readFieldBlacklist, writeFieldBlacklist, statusId);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling GroupsApi.UpdatePrivileges: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **groupId** | **string**| ID of group to return | 
 **tableNameOrPrivilegeId** | **string**| ID of privileges or Table Name to use | 
 **privilegesId** | **string**| ubique privilege ID | [optional] 
 **groupId2** | **string**| ID of group to return | [optional] 
 **tableName** | **string**| Name of table to add | [optional] 
 **allowAdd** | **int?**| Permission to add/create entries in the table (See values below) | [optional] 
 **allowEdit** | **int?**| Permission to edit/update entries in the table (See values below) | [optional] 
 **allowDelete** | **int?**| Permission to delete/remove entries in the table (See values below) | [optional] 
 **allowView** | **int?**| Permission to view/read entries in the table (See values below) | [optional] 
 **allowAlter** | **int?**| Permission to add/create entries in the table (See values below) | [optional] 
 **navListed** | **bool?**| If the table should be visible in the sidebar for this user group | [optional] 
 **readFieldBlacklist** | **string**| A CSV of column names that the group can&#39;t view (read) | [optional] 
 **writeFieldBlacklist** | **string**| A CSV of column names that the group can&#39;t edit (update) | [optional] 
 **statusId** | **string**| State of the record that this permissions belongs to (Draft, Active or Soft Deleted) | [optional] 

### Return type

void (empty response body)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: application/x-www-form-urlencoded
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

