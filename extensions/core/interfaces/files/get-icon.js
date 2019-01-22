export default function getIcon(type) {
  if (type === "application/pdf") {
    return "picture_as_pdf";
  }

  if (type.startsWith("application")) {
    return "insert_drive_file";
  }

  if (type.startsWith("image")) {
    return "crop_original";
  }

  if (type.startsWith("video")) {
    return "videocam";
  }

  if (type.startsWith("code")) {
    return "code";
  }

  if (type.startsWith("audio")) {
    return "audiotrack";
  }

  return "save";
}
