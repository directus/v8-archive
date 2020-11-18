#import "DRCTSGetActivityData.h"

@implementation DRCTSGetActivityData

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
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"_id": @"id", @"identifier": @"identifier", @"action": @"action", @"tableName": @"table_name", @"rowId": @"row_id", @"user": @"user", @"datetime": @"datetime", @"type": @"type", @"data": @"data" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"_id", @"identifier", @"action", @"tableName", @"rowId", @"user", @"datetime", @"type", @"data"];
  return [optionalProperties containsObject:propertyName];
}

@end
