#import "DRCTSGetPrivilegesData.h"

@implementation DRCTSGetPrivilegesData

- (instancetype)init {
  self = [super init];
  if (self) {
    // initialize property's default value, if any
    
  }
  return self;
}


/**
 * Maps json key to property name.
 * This method is used by `JSONModel`.
 */
+ (JSONKeyMapper *)keyMapper {
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"_id": @"id", @"tableName": @"table_name", @"groupId": @"group_id", @"readFieldBlacklist": @"read_field_blacklist", @"writeFieldBlacklist": @"write_field_blacklist", @"navListed": @"nav_listed", @"statusId": @"status_id", @"allowView": @"allow_view", @"allowAdd": @"allow_add", @"allowEdit": @"allow_edit", @"allowDelete": @"allow_delete", @"allowAlter": @"allow_alter" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"_id", @"tableName", @"groupId", @"readFieldBlacklist", @"writeFieldBlacklist", @"navListed", @"statusId", @"allowView", @"allowAdd", @"allowEdit", @"allowDelete", @"allowAlter"];
  return [optionalProperties containsObject:propertyName];
}

@end
