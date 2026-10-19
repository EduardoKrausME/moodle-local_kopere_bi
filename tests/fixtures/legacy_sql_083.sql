SELECT kbi_fi.id,
       kbi_fi.name,
       CASE
         WHEN kbi_fi.typ = 'multichoicerated'
                 THEN AVG(
                        SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(kbi_fi.presentation, '>>>>>', '|'), '
                                        -1))
         ELSE AVG(kbi_fv.value)
           END AS avg_value
FROM mdl_feedback kbi_f
       JOIN mdl_feedback_completed kbi_fc ON kbi_fc.feedback = kbi_f.id
       JOIN mdl_feedback_item kbi_fi ON kbi_fi.feedback = kbi_f.id
       JOIN mdl_feedback_value kbi_fv ON kbi_fv.completed = kbi_fc.id AND kbi_fv.item = kbi_fi.id
       LEFT JOIN mdl_modules kbi_m ON kbi_m.name = 'feedback'
       LEFT JOIN mdl_course_modules kbi_cm ON kbi_cm.module = kbi_m.id AND kbi_cm.instance = kbi_f.id
WHERE kbi_f.id > 0
GROUP BY kbi_fi.id