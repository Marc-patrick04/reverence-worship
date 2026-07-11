import { NextRequest } from "next/server";
import { requireAdminUser } from "@/lib/auth";
import { getUserExportRows } from "@/lib/user-export-data";

const headers = [
  "#",
  "Name",
  "Email",
  "Phone",
  "Role",
  "Status",
  "DOB",
  "Gender",
  "Marital Status",
  "Residence",
  "Family",
  "Occupation",
];

function csvCell(value: string | number) {
  const text = String(value);
  return `"${text.replaceAll('"', '""')}"`;
}

export async function GET(request: NextRequest) {
  await requireAdminUser();

  const rows = await getUserExportRows({
    search: request.nextUrl.searchParams.get("search"),
    role: request.nextUrl.searchParams.get("role"),
    status: request.nextUrl.searchParams.get("status"),
  });

  const csvRows = [
    headers.map(csvCell).join(","),
    ...rows.map((row) =>
      [
        row.index,
        row.name,
        row.email,
        row.phone,
        row.role,
        row.status,
        row.dob,
        row.gender,
        row.maritalStatus,
        row.residence,
        row.family,
        row.occupation,
      ]
        .map(csvCell)
        .join(","),
    ),
  ];

  const filename = `users_export_${new Date().toISOString().slice(0, 19).replaceAll(":", "-")}.csv`;

  return new Response(`\uFEFF${csvRows.join("\r\n")}`, {
    headers: {
      "Content-Type": "text/csv; charset=UTF-8",
      "Content-Disposition": `attachment; filename="${filename}"`,
    },
  });
}
