SELECT kbi_fi.id,
       kbi_fi.name,
       AVG(CASE WHEN kbi_fv.value REGEXP '^-?[0-9]+([.][0-9]+)?$'
                THEN CAST(kbi_fv.value AS DECIMAL(12, 4))
                ELSE NULL END) AS avg_value
FROM mdl_feedback kbi_f
JOIN mdl_feedback_completed kbi_fc ON kbi_fc.feedback = kbi_f.id
JOIN mdl_feedback_item kbi_fi ON kbi_fi.feedback = kbi_f.id
JOIN mdl_feedback_value kbi_fv ON kbi_fv.completed = kbi_fc.id AND kbi_fv.item = kbi_fi.id
WHERE kbi_f.id > 0
GROUP BY kbi_fi.id, kbi_fi.name