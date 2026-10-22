/**
 * Convert the MySQL expressions used by the bundled Kopere BI reports to PostgreSQL.
 *
 * This is a build-time helper. Generated PostgreSQL report definitions are committed
 * separately, keeping the original MySQL SQL untouched.
 */
"use strict";

function mysqlToPostgresSql(source) {
    function splitArguments(source) {
        const argumentsList = [];
        let depth = 0;
        let quote = "";
        let start = 0;
        for (let index = 0; index < source.length; index++) {
            const character = source[index];
            if (quote) {
                if (character === quote) {
                    if (source[index + 1] === quote) {
                        index++;
                        continue;
                    }
                    quote = "";
                } else if (character === "\\" && index + 1 < source.length) {
                    index++;
                }
                continue;
            }
            if (character === "'" || character === '"' || character === "\x60") {
                quote = character;
            } else if (character === "(") {
                depth++;
            } else if (character === ")") {
                depth--;
            } else if (character === "," && depth === 0) {
                argumentsList.push(source.slice(start, index).trim());
                start = index + 1;
            }
        }
        argumentsList.push(source.slice(start).trim());
        return argumentsList;
    }

    function postgresDateFormat(format) {
        return format.replace(/%[YymdHisTbe]/g, function (token) {
            return {
                "%Y": "YYYY", "%y": "YY", "%m": "MM", "%d": "DD",
                "%H": "HH24", "%i": "MI", "%s": "SS",
                "%T": "HH24:MI:SS", "%b": "Mon", "%e": "FMDD"
            }[token] || token;
        });
    }

    function scan(source) {
        let output = "";
        let index = 0;
        while (index < source.length) {
            if (source[index] === "'") {
                let end = index + 1;
                for (; end < source.length; end++) {
                    if (source[end] === "'" && source[end + 1] === "'") {
                        end++;
                        continue;
                    }
                    if (source[end] === "'") {
                        end++;
                        break;
                    }
                }
                output += source.slice(index, end);
                index = end;
                continue;
            }

            const match = /^([a-z_][a-z_0-9]*)\s*\(/i.exec(source.slice(index));
            if (!match || (index > 0 && /[\w.]/.test(source[index - 1]))) {
                output += source[index++];
                continue;
            }

            const name = match[1].toUpperCase();
            const start = index + match[0].length;
            let end = start;
            let depth = 1;
            let quote = "";
            for (; end < source.length; end++) {
                const character = source[end];
                if (quote) {
                    if (character === quote) {
                        if (source[end + 1] === quote) {
                            end++;
                            continue;
                        }
                        quote = "";
                    } else if (character === "\\" && end + 1 < source.length) {
                        end++;
                    }
                    continue;
                }
                if (character === "'") {
                    quote = character;
                } else if (character === "(") {
                    depth++;
                } else if (character === ")" && --depth === 0) {
                    break;
                }
            }
            if (depth !== 0) {
                output += source[index++];
                continue;
            }

            const inner = scan(source.slice(start, end));
            const args = splitArguments(inner);
            let replacement = null;
            switch (name) {
                case "UNIX_TIMESTAMP":
                    replacement = "(EXTRACT(EPOCH FROM " +
                        (args[0] || "CURRENT_TIMESTAMP") + ")::bigint)";
                    break;
                case "FROM_UNIXTIME":
                    replacement = args.length > 1 ?
                        "TO_CHAR(TO_TIMESTAMP(" + args[0] + "), '" +
                            postgresDateFormat(args[1].replace(/^'|'$/g, "")) + "')" :
                        "TO_TIMESTAMP(" + args[0] + ")";
                    break;
                case "DATE_FORMAT":
                    replacement = "TO_CHAR(" + args[0] + ", '" +
                        postgresDateFormat(args[1].replace(/^'|'$/g, "")) + "')";
                    break;
                case "NOW":
                    replacement = "CURRENT_TIMESTAMP";
                    break;
                case "CURDATE":
                    replacement = "CURRENT_DATE";
                    break;
                case "DATEDIFF":
                    replacement = "((" + args[0] + ")::date - (" + args[1] + ")::date)";
                    break;
                case "WEEKDAY":
                    replacement = "(EXTRACT(ISODOW FROM " + args[0] + ")::integer - 1)";
                    break;
                case "HOUR":
                    replacement = "EXTRACT(HOUR FROM " + args[0] + ")";
                    break;
                case "DAYOFMONTH":
                    replacement = "EXTRACT(DAY FROM " + args[0] + ")";
                    break;
                case "MONTH":
                    replacement = "EXTRACT(MONTH FROM " + args[0] + ")";
                    break;
                case "YEAR":
                    replacement = "EXTRACT(YEAR FROM " + args[0] + ")";
                    break;
                case "TIME_TO_SEC":
                    replacement = "EXTRACT(EPOCH FROM NULLIF((" +
                        args[0] + ")::text, '')::interval)";
                    break;
                case "IFNULL":
                    replacement = "COALESCE(" + args.join(", ") + ")";
                    break;
                case "ROUND":
                    if (args.length === 2) {
                        replacement = "ROUND((" + args[0] + ")::numeric, " + args[1] + ")";
                    }
                    break;
                case "DATE_SUB":
                case "DATE_ADD": {
                    const interval = /^\s*INTERVAL\s+(\d+)\s+(\w+)\s*$/i.exec(args[1] || "");
                    if (interval) {
                        replacement = "(" + args[0] + (name === "DATE_SUB" ? " - " : " + ") +
                            "INTERVAL '" + interval[1] + " " + interval[2].toLowerCase() + "')";
                    }
                    break;
                }
                case "PERIOD_DIFF":
                    replacement = "((SUBSTRING(" + args[0] +
                        " FROM 1 FOR 4)::integer - SUBSTRING(" + args[1] +
                        " FROM 1 FOR 4)::integer) * 12 + (RIGHT(" + args[0] +
                        ", 2)::integer - RIGHT(" + args[1] + ", 2)::integer))";
                    break;
                case "GROUP_CONCAT": {
                    const format = /^\s*(DISTINCT\s*)?([\s\S]*?)(?:\s+ORDER\s+BY\s+[\s\S]*?)?(?:\s+SEPARATOR\s+'((?:''|[^'])*)')?\s*$/i.exec(inner);
                    if (!format) {
                        throw new Error("Unsupported GROUP_CONCAT: " + inner);
                    }
                    const expression = format[2].trim();
                    const distinct = !!format[1];
                    const separator = format[3] === undefined ? "," : format[3];
                    const ordered = /\s+ORDER\s+BY\s+/i.test(inner);
                    replacement = "STRING_AGG(" + (distinct ? "DISTINCT " : "") +
                        "(" + expression + ")::text, '" + separator.replace(/'/g, "''") + "'" +
                        (ordered ? " ORDER BY (" + expression + ")::text" : "") + ")";
                    break;
                }
            }
            output += replacement === null ? match[1] + "(" + inner + ")" : replacement;
            index = end + 1;
        }
        return output;
    }

    return scan(source).replace(/\bAS\s+'((?:''|[^'])*)'/gi, function (match, value) {
        return "AS " + '"' + value.replace(/''/g, "'") + '"';
    });
}
