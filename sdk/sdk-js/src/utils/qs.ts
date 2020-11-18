export type QuerifySerializer = (key: string, value: string | number | boolean) => string;

const defaultSerializeTransform: QuerifySerializer = (key, value) => `${key}=${encodeURIComponent(value)}`;

export function querify(
  obj: { [index: string]: any },
  prefix?: string,
  serializer: QuerifySerializer = defaultSerializeTransform
): string {
  let qs: string[] = [],
    prop: string;

  for (prop in obj) {
    if (obj.hasOwnProperty(prop)) {
      const key = prefix ? `${prefix}[${prop}]` : prop;
      const val = obj[prop];

      qs.push(val !== null && typeof val === "object" ? querify(val, key) : serializer(key, val));
    }
  }

  return qs.join("&");
}
