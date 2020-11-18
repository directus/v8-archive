#import "DRCTSGroupsApi.h"
#import "DRCTSQueryParamCollection.h"
#import "DRCTSApiClient.h"
#import "DRCTSGetGroup.h"
#import "DRCTSGetGroups.h"
#import "DRCTSGetPrivileges.h"
#import "DRCTSGetPrivilegesForTable.h"


@interface DRCTSGroupsApi ()

@property (nonatomic, strong, readwrite) NSMutableDictionary *mutableDefaultHeaders;

@end

@implementation DRCTSGroupsApi

NSString* kDRCTSGroupsApiErrorDomain = @"DRCTSGroupsApiErrorDomain";
NSInteger kDRCTSGroupsApiMissingParamErrorCode = 234513;

@synthesize apiClient = _apiClient;

#pragma mark - Initialize methods

- (instancetype) init {
    return [self initWithApiClient:[DRCTSApiClient sharedClient]];
}


-(instancetype) initWithApiClient:(DRCTSApiClient *)apiClient {
    self = [super init];
    if (self) {
        _apiClient = apiClient;
        _mutableDefaultHeaders = [NSMutableDictionary dictionary];
    }
    return self;
}

#pragma mark -

-(NSString*) defaultHeaderForKey:(NSString*)key {
    return self.mutableDefaultHeaders[key];
}

-(void) setDefaultHeaderValue:(NSString*) value forKey:(NSString*)key {
    [self.mutableDefaultHeaders setValue:value forKey:key];
}

-(NSDictionary *)defaultHeaders {
    return self.mutableDefaultHeaders;
}

#pragma mark - Api Methods

///
/// Add a new group
/// 
///  @param name Name of group to add (optional)
///
///  @returns void
///
-(NSURLSessionTask*) addGroupWithName: (NSString*) name
    completionHandler: (void (^)(NSError* error)) handler {
    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/groups"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[@"application/x-www-form-urlencoded"]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];
    if (name) {
        formParams[@"name"] = name;
    }

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"POST"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: nil
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler(error);
                                }
                            }];
}

///
/// Create new table privileges for the specified user group
/// 
///  @param groupId ID of group to return 
///
///  @param _id Privilege's Unique Identification number (optional)
///
///  @param tableName Name of table to add (optional)
///
///  @param allowAdd Permission to add/create entries in the table (See values below) (optional)
///
///  @param allowEdit Permission to edit/update entries in the table (See values below) (optional)
///
///  @param allowDelete Permission to delete/remove entries in the table (See values below) (optional)
///
///  @param allowView Permission to view/read entries in the table (See values below) (optional)
///
///  @param allowAlter Permission to add/create entries in the table (See values below) (optional)
///
///  @param navListed If the table should be visible in the sidebar for this user group (optional)
///
///  @param readFieldBlacklist A CSV of column names that the group can't view (read) (optional)
///
///  @param writeFieldBlacklist A CSV of column names that the group can't edit (update) (optional)
///
///  @param statusId State of the record that this permissions belongs to (Draft, Active or Soft Deleted) (optional)
///
///  @returns void
///
-(NSURLSessionTask*) addPrivilegeWithGroupId: (NSString*) groupId
    _id: (NSNumber*) _id
    tableName: (NSString*) tableName
    allowAdd: (NSNumber*) allowAdd
    allowEdit: (NSNumber*) allowEdit
    allowDelete: (NSNumber*) allowDelete
    allowView: (NSNumber*) allowView
    allowAlter: (NSNumber*) allowAlter
    navListed: (NSNumber*) navListed
    readFieldBlacklist: (NSString*) readFieldBlacklist
    writeFieldBlacklist: (NSString*) writeFieldBlacklist
    statusId: (NSString*) statusId
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'groupId' is set
    if (groupId == nil) {
        NSParameterAssert(groupId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"groupId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSGroupsApiErrorDomain code:kDRCTSGroupsApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/privileges/{groupId}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (groupId != nil) {
        pathParams[@"groupId"] = groupId;
    }

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[@"application/x-www-form-urlencoded"]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];
    if (_id) {
        formParams[@"id"] = _id;
    }
    if (tableName) {
        formParams[@"table_name"] = tableName;
    }
    if (allowAdd) {
        formParams[@"allow_add"] = allowAdd;
    }
    if (allowEdit) {
        formParams[@"allow_edit"] = allowEdit;
    }
    if (allowDelete) {
        formParams[@"allow_delete"] = allowDelete;
    }
    if (allowView) {
        formParams[@"allow_view"] = allowView;
    }
    if (allowAlter) {
        formParams[@"allow_alter"] = allowAlter;
    }
    if (navListed) {
        formParams[@"nav_listed"] = navListed;
    }
    if (readFieldBlacklist) {
        formParams[@"read_field_blacklist"] = readFieldBlacklist;
    }
    if (writeFieldBlacklist) {
        formParams[@"write_field_blacklist"] = writeFieldBlacklist;
    }
    if (statusId) {
        formParams[@"status_id"] = statusId;
    }

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"POST"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: nil
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler(error);
                                }
                            }];
}

///
/// Returns specific group
/// 
///  @param groupId ID of group to return 
///
///  @returns DRCTSGetGroup*
///
-(NSURLSessionTask*) getGroupWithGroupId: (NSString*) groupId
    completionHandler: (void (^)(DRCTSGetGroup* output, NSError* error)) handler {
    // verify the required parameter 'groupId' is set
    if (groupId == nil) {
        NSParameterAssert(groupId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"groupId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSGroupsApiErrorDomain code:kDRCTSGroupsApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/groups/{groupId}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (groupId != nil) {
        pathParams[@"groupId"] = groupId;
    }

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"GET"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: @"DRCTSGetGroup*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetGroup*)data, error);
                                }
                            }];
}

///
/// Returns groups
/// 
///  @returns DRCTSGetGroups*
///
-(NSURLSessionTask*) getGroupsWithCompletionHandler: 
    (void (^)(DRCTSGetGroups* output, NSError* error)) handler {
    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/groups"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"GET"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: @"DRCTSGetGroups*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetGroups*)data, error);
                                }
                            }];
}

///
/// Returns group privileges
/// 
///  @param groupId ID of group to return 
///
///  @returns DRCTSGetPrivileges*
///
-(NSURLSessionTask*) getPrivilegesWithGroupId: (NSString*) groupId
    completionHandler: (void (^)(DRCTSGetPrivileges* output, NSError* error)) handler {
    // verify the required parameter 'groupId' is set
    if (groupId == nil) {
        NSParameterAssert(groupId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"groupId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSGroupsApiErrorDomain code:kDRCTSGroupsApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/privileges/{groupId}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (groupId != nil) {
        pathParams[@"groupId"] = groupId;
    }

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"GET"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: @"DRCTSGetPrivileges*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetPrivileges*)data, error);
                                }
                            }];
}

///
/// Returns group privileges by tableName
/// 
///  @param groupId ID of group to return 
///
///  @param tableNameOrPrivilegeId ID of privileges or Table Name to use 
///
///  @returns DRCTSGetPrivilegesForTable*
///
-(NSURLSessionTask*) getPrivilegesForTableWithGroupId: (NSString*) groupId
    tableNameOrPrivilegeId: (NSString*) tableNameOrPrivilegeId
    completionHandler: (void (^)(DRCTSGetPrivilegesForTable* output, NSError* error)) handler {
    // verify the required parameter 'groupId' is set
    if (groupId == nil) {
        NSParameterAssert(groupId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"groupId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSGroupsApiErrorDomain code:kDRCTSGroupsApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    // verify the required parameter 'tableNameOrPrivilegeId' is set
    if (tableNameOrPrivilegeId == nil) {
        NSParameterAssert(tableNameOrPrivilegeId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableNameOrPrivilegeId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSGroupsApiErrorDomain code:kDRCTSGroupsApiMissingParamErrorCode userInfo:userInfo];
            handler(nil, error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/privileges/{groupId}/{tableNameOrPrivilegeId}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (groupId != nil) {
        pathParams[@"groupId"] = groupId;
    }
    if (tableNameOrPrivilegeId != nil) {
        pathParams[@"tableNameOrPrivilegeId"] = tableNameOrPrivilegeId;
    }

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"GET"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: @"DRCTSGetPrivilegesForTable*"
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler((DRCTSGetPrivilegesForTable*)data, error);
                                }
                            }];
}

///
/// Update privileges by privilegeId
/// 
///  @param groupId ID of group to return 
///
///  @param tableNameOrPrivilegeId ID of privileges or Table Name to use 
///
///  @param privilegesId ubique privilege ID (optional)
///
///  @param groupId2 ID of group to return (optional)
///
///  @param tableName Name of table to add (optional)
///
///  @param allowAdd Permission to add/create entries in the table (See values below) (optional)
///
///  @param allowEdit Permission to edit/update entries in the table (See values below) (optional)
///
///  @param allowDelete Permission to delete/remove entries in the table (See values below) (optional)
///
///  @param allowView Permission to view/read entries in the table (See values below) (optional)
///
///  @param allowAlter Permission to add/create entries in the table (See values below) (optional)
///
///  @param navListed If the table should be visible in the sidebar for this user group (optional)
///
///  @param readFieldBlacklist A CSV of column names that the group can't view (read) (optional)
///
///  @param writeFieldBlacklist A CSV of column names that the group can't edit (update) (optional)
///
///  @param statusId State of the record that this permissions belongs to (Draft, Active or Soft Deleted) (optional)
///
///  @returns void
///
-(NSURLSessionTask*) updatePrivilegesWithGroupId: (NSString*) groupId
    tableNameOrPrivilegeId: (NSString*) tableNameOrPrivilegeId
    privilegesId: (NSString*) privilegesId
    groupId2: (NSString*) groupId2
    tableName: (NSString*) tableName
    allowAdd: (NSNumber*) allowAdd
    allowEdit: (NSNumber*) allowEdit
    allowDelete: (NSNumber*) allowDelete
    allowView: (NSNumber*) allowView
    allowAlter: (NSNumber*) allowAlter
    navListed: (NSNumber*) navListed
    readFieldBlacklist: (NSString*) readFieldBlacklist
    writeFieldBlacklist: (NSString*) writeFieldBlacklist
    statusId: (NSString*) statusId
    completionHandler: (void (^)(NSError* error)) handler {
    // verify the required parameter 'groupId' is set
    if (groupId == nil) {
        NSParameterAssert(groupId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"groupId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSGroupsApiErrorDomain code:kDRCTSGroupsApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    // verify the required parameter 'tableNameOrPrivilegeId' is set
    if (tableNameOrPrivilegeId == nil) {
        NSParameterAssert(tableNameOrPrivilegeId);
        if(handler) {
            NSDictionary * userInfo = @{NSLocalizedDescriptionKey : [NSString stringWithFormat:NSLocalizedString(@"Missing required parameter '%@'", nil),@"tableNameOrPrivilegeId"] };
            NSError* error = [NSError errorWithDomain:kDRCTSGroupsApiErrorDomain code:kDRCTSGroupsApiMissingParamErrorCode userInfo:userInfo];
            handler(error);
        }
        return nil;
    }

    NSMutableString* resourcePath = [NSMutableString stringWithFormat:@"/privileges/{groupId}/{tableNameOrPrivilegeId}"];

    NSMutableDictionary *pathParams = [[NSMutableDictionary alloc] init];
    if (groupId != nil) {
        pathParams[@"groupId"] = groupId;
    }
    if (tableNameOrPrivilegeId != nil) {
        pathParams[@"tableNameOrPrivilegeId"] = tableNameOrPrivilegeId;
    }

    NSMutableDictionary* queryParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary* headerParams = [NSMutableDictionary dictionaryWithDictionary:self.apiClient.configuration.defaultHeaders];
    [headerParams addEntriesFromDictionary:self.defaultHeaders];
    // HTTP header `Accept`
    NSString *acceptHeader = [self.apiClient.sanitizer selectHeaderAccept:@[@"application/json"]];
    if(acceptHeader.length > 0) {
        headerParams[@"Accept"] = acceptHeader;
    }

    // response content type
    NSString *responseContentType = [[acceptHeader componentsSeparatedByString:@", "] firstObject] ?: @"";

    // request content type
    NSString *requestContentType = [self.apiClient.sanitizer selectHeaderContentType:@[@"application/x-www-form-urlencoded"]];

    // Authentication setting
    NSArray *authSettings = @[@"api_key"];

    id bodyParam = nil;
    NSMutableDictionary *formParams = [[NSMutableDictionary alloc] init];
    NSMutableDictionary *localVarFiles = [[NSMutableDictionary alloc] init];
    if (privilegesId) {
        formParams[@"privileges_id"] = privilegesId;
    }
    if (groupId2) {
        formParams[@"group_id"] = groupId2;
    }
    if (tableName) {
        formParams[@"table_name"] = tableName;
    }
    if (allowAdd) {
        formParams[@"allow_add"] = allowAdd;
    }
    if (allowEdit) {
        formParams[@"allow_edit"] = allowEdit;
    }
    if (allowDelete) {
        formParams[@"allow_delete"] = allowDelete;
    }
    if (allowView) {
        formParams[@"allow_view"] = allowView;
    }
    if (allowAlter) {
        formParams[@"allow_alter"] = allowAlter;
    }
    if (navListed) {
        formParams[@"nav_listed"] = navListed;
    }
    if (readFieldBlacklist) {
        formParams[@"read_field_blacklist"] = readFieldBlacklist;
    }
    if (writeFieldBlacklist) {
        formParams[@"write_field_blacklist"] = writeFieldBlacklist;
    }
    if (statusId) {
        formParams[@"status_id"] = statusId;
    }

    return [self.apiClient requestWithPath: resourcePath
                                    method: @"PUT"
                                pathParams: pathParams
                               queryParams: queryParams
                                formParams: formParams
                                     files: localVarFiles
                                      body: bodyParam
                              headerParams: headerParams
                              authSettings: authSettings
                        requestContentType: requestContentType
                       responseContentType: responseContentType
                              responseType: nil
                           completionBlock: ^(id data, NSError *error) {
                                if(handler) {
                                    handler(error);
                                }
                            }];
}



@end
