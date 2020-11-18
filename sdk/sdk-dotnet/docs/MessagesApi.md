# IO.Directus.Api.MessagesApi

All URIs are relative to your configured base path, as per example.

Method | HTTP request | Description
------------- | ------------- | -------------
[**GetMessage**](MessagesApi.md#getmessage) | **GET** /messages/{messageId} | Returns specific message
[**GetMessages**](MessagesApi.md#getmessages) | **GET** /messages/self | Returns messages


<a name="getmessage"></a>
# **GetMessage**
> GetMessage GetMessage (int? messageId)

Returns specific message

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetMessageExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new MessagesApi();
            var messageId = 56;  // int? | ID of message to return

            try
            {
                // Returns specific message
                GetMessage result = apiInstance.GetMessage(messageId);
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling MessagesApi.GetMessage: " + e.Message );
            }
        }
    }
}
```

### Parameters

Name | Type | Description  | Notes
------------- | ------------- | ------------- | -------------
 **messageId** | **int?**| ID of message to return | 

### Return type

[**GetMessage**](GetMessage.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

<a name="getmessages"></a>
# **GetMessages**
> GetMessages GetMessages ()

Returns messages

### Example
```csharp
using System;
using System.Diagnostics;
using IO.Directus.Api;
using IO.Directus.Client;
using IO.Directus.Model;

namespace Example
{
    public class GetMessagesExample
    {
        public void main()
        {
            // Configure API key authorization: api_key
            Configuration.Default.AddApiKey("access_token", "YOUR_API_KEY");
            // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
            // Configuration.Default.AddApiKeyPrefix("access_token", "Bearer");

            Configuration.Default.BasePath = "https://myinstance.directus.io/api/1.1";
            
            var apiInstance = new MessagesApi();

            try
            {
                // Returns messages
                GetMessages result = apiInstance.GetMessages();
                Debug.WriteLine(result);
            }
            catch (Exception e)
            {
                Debug.Print("Exception when calling MessagesApi.GetMessages: " + e.Message );
            }
        }
    }
}
```

### Parameters
This endpoint does not need any parameter.

### Return type

[**GetMessages**](GetMessages.md)

### Authorization

[api_key](../README.md#api_key)

### HTTP request headers

 - **Content-Type**: Not defined
 - **Accept**: application/json

[[Back to top]](#) [[Back to API list]](../README.md#documentation-for-api-endpoints) [[Back to Model list]](../README.md#documentation-for-models) [[Back to README]](../README.md)

