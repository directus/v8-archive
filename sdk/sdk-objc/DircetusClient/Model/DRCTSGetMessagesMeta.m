#import "DRCTSGetMessagesMeta.h"

@implementation DRCTSGetMessagesMeta

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
  return [[JSONKeyMapper alloc] initWithModelToJSONDictionary:@{ @"read": @"read", @"unread": @"unread", @"total": @"total", @"maxId": @"max_id", @"type": @"type", @"table": @"table" }];
}

/**
 * Indicates whether the property with the given name is optional.
 * If `propertyName` is optional, then return `YES`, otherwise return `NO`.
 * This method is used by `JSONModel`.
 */
+ (BOOL)propertyIsOptional:(NSString *)propertyName {

  NSArray *optionalProperties = @[@"read", @"unread", @"total", @"maxId", @"type", @"table"];
  return [optionalProperties containsObject:propertyName];
}

@end
