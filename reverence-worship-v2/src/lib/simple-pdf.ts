function escapePdfText(value: string | number) {
  return String(value)
    .replaceAll("\\", "\\\\")
    .replaceAll("(", "\\(")
    .replaceAll(")", "\\)")
    .replaceAll("\r", " ")
    .replaceAll("\n", " ");
}

function chunkText(value: string | number, maxLength: number) {
  const text = String(value);
  return text.length > maxLength ? `${text.slice(0, Math.max(0, maxLength - 1))}…` : text;
}

export type PdfTextLine = {
  text: string;
  x: number;
  y: number;
  size?: number;
  bold?: boolean;
};

export function createSimplePdf(lines: PdfTextLine[], options?: { landscape?: boolean }) {
  const width = options?.landscape ? 842 : 595;
  const height = options?.landscape ? 595 : 842;
  const objects: string[] = [];

  const content = [
    "BT",
    ...lines.map((line) => {
      const font = line.bold ? "F2" : "F1";
      const size = line.size ?? 9;
      return `/${font} ${size} Tf ${line.x} ${line.y} Td (${escapePdfText(line.text)}) Tj`;
    }),
    "ET",
  ].join("\n");

  objects.push("<< /Type /Catalog /Pages 2 0 R >>");
  objects.push("<< /Type /Pages /Kids [3 0 R] /Count 1 >>");
  objects.push(
    `<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ${width} ${height}] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>`,
  );
  objects.push("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>");
  objects.push("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>");
  objects.push(`<< /Length ${Buffer.byteLength(content)} >>\nstream\n${content}\nendstream`);

  let pdf = "%PDF-1.4\n";
  const offsets = [0];

  objects.forEach((object, index) => {
    offsets.push(Buffer.byteLength(pdf));
    pdf += `${index + 1} 0 obj\n${object}\nendobj\n`;
  });

  const xrefOffset = Buffer.byteLength(pdf);
  pdf += `xref\n0 ${objects.length + 1}\n`;
  pdf += "0000000000 65535 f \n";
  offsets.slice(1).forEach((offset) => {
    pdf += `${String(offset).padStart(10, "0")} 00000 n \n`;
  });
  pdf += `trailer\n<< /Size ${objects.length + 1} /Root 1 0 R >>\nstartxref\n${xrefOffset}\n%%EOF`;

  return Buffer.from(pdf);
}

export function pdfCell(value: string | number, maxLength: number) {
  return chunkText(value, maxLength).replace(/[^\x20-\x7E]/g, "");
}
