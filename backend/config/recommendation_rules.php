<?php
// config/recommendation_rules.php

return [
    'math' => [
        0 => [
            'activity'     => "Math: No classes attended. Encourage attendance and catch-up worksheets.",
            'category'     => 'attendance',
            'priority'     => 'high',
            'guardian_tip' => "Ensure the student attends scheduled classes and provide home supervision.",
            'student_tip'  => "Try to attend every math class — consistency builds confidence.",
        ],
        1 => [
            'activity'     => "Math: Poor rating. Assign daily drills, tutoring, and multiplication practice.",
            'category'     => 'remedial',
            'priority'     => 'high',
            'guardian_tip' => "Arrange short daily review sessions and consider a tutor.",
            'student_tip'  => "Practice multiplication tables for 10 minutes daily.",
        ],
        2 => [
            'activity'     => "Math: Good rating. Reinforce with worksheets and applied problem-solving.",
            'category'     => 'reinforcement',
            'priority'     => 'medium',
            'guardian_tip' => "Encourage problem-solving games at home.",
            'student_tip'  => "Keep practicing word problems to strengthen skills.",
        ],
        3 => [
            'activity'     => "Math: Very Good. Introduce enrichment puzzles and advanced exercises.",
            'category'     => 'enrichment',
            'priority'     => 'low',
            'guardian_tip' => "Provide math puzzles or logic games.",
            'student_tip'  => "Challenge yourself with advanced exercises.",
        ],
        4 => [
            'activity'     => "Math: Excellent. Encourage peer tutoring, projects, and competitions.",
            'category'     => 'leadership',
            'priority'     => 'low',
            'guardian_tip' => "Support participation in math clubs or competitions.",
            'student_tip'  => "Help classmates by tutoring — teaching reinforces learning.",
        ],
    ],

    'science' => [
        0 => [
            'activity'     => "Science: No classes attended. Encourage attendance and lab participation.",
            'category'     => 'attendance',
            'priority'     => 'high',
            'guardian_tip' => "Ensure lab attendance and encourage curiosity at home.",
            'student_tip'  => "Attend every science class to avoid missing experiments.",
        ],
        1 => [
            'activity'     => "Science: Poor rating. Assign review of lab notes and guided experiments.",
            'category'     => 'remedial',
            'priority'     => 'high',
            'guardian_tip' => "Review lab notes together and supervise simple experiments.",
            'student_tip'  => "Revisit your lab notes daily to reinforce concepts.",
        ],
        2 => [
            'activity'     => "Science: Good rating. Reinforce with observation journals and home experiments.",
            'category'     => 'reinforcement',
            'priority'     => 'medium',
            'guardian_tip' => "Encourage keeping a science journal.",
            'student_tip'  => "Try simple home experiments to apply what you learn.",
        ],
        3 => [
            'activity'     => "Science: Very Good. Encourage deeper research projects and group discussions.",
            'category'     => 'enrichment',
            'priority'     => 'low',
            'guardian_tip' => "Support participation in group projects.",
            'student_tip'  => "Explore topics beyond the textbook through research.",
        ],
        4 => [
            'activity'     => "Science: Excellent. Promote independent research and science fairs.",
            'category'     => 'leadership',
            'priority'     => 'low',
            'guardian_tip' => "Encourage joining science fairs or clubs.",
            'student_tip'  => "Take initiative in independent research projects.",
        ],
    ],

    'english' => [
        0 => [
            'activity'     => "English: No classes attended. Encourage attendance and reading practice.",
            'category'     => 'attendance',
            'priority'     => 'high',
            'guardian_tip' => "Ensure daily reading time at home.",
            'student_tip'  => "Attend classes and read short stories daily.",
        ],
        1 => [
            'activity'     => "English: Poor rating. Assign daily reading aloud and comprehension drills.",
            'category'     => 'remedial',
            'priority'     => 'high',
            'guardian_tip' => "Listen to the student read aloud and correct pronunciation.",
            'student_tip'  => "Read aloud for 15 minutes daily to improve fluency.",
        ],
        2 => [
            'activity'     => "English: Good rating. Reinforce with vocabulary building and writing tasks.",
            'category'     => 'reinforcement',
            'priority'     => 'medium',
            'guardian_tip' => "Encourage writing short essays or diary entries.",
            'student_tip'  => "Learn 5 new words daily and use them in sentences.",
        ],
        3 => [
            'activity'     => "English: Very Good. Encourage creative writing and debate practice.",
            'category'     => 'enrichment',
            'priority'     => 'low',
            'guardian_tip' => "Support participation in debates or writing contests.",
            'student_tip'  => "Write creative stories or join debate clubs.",
        ],
        4 => [
            'activity'     => "English: Excellent. Promote advanced literature analysis and peer tutoring.",
            'category'     => 'leadership',
            'priority'     => 'low',
            'guardian_tip' => "Encourage analysis of novels and peer tutoring.",
            'student_tip'  => "Help classmates with reading comprehension.",
        ],
    ],

    'filipino' => [
        0 => [
            'activity'     => "Filipino: No classes attended. Encourage attendance and cultural engagement.",
            'category'     => 'attendance',
            'priority'     => 'high',
            'guardian_tip' => "Promote cultural activities at home.",
            'student_tip'  => "Attend Filipino classes and engage with cultural texts.",
        ],
        1 => [
            'activity'     => "Filipino: Poor rating. Assign vocabulary flashcards and conversational practice.",
            'category'     => 'remedial',
            'priority'     => 'high',
            'guardian_tip' => "Practice conversational Filipino at home.",
            'student_tip'  => "Use flashcards daily to improve vocabulary.",
        ],
        2 => [
            'activity'     => "Filipino: Good rating. Reinforce with dialogues and cultural text reading.",
            'category'     => 'reinforcement',
            'priority'     => 'medium',
            'guardian_tip' => "Encourage reading Filipino short stories.",
            'student_tip'  => "Practice dialogues with peers.",
        ],
        3 => [
            'activity'     => "Filipino: Very Good. Encourage essay writing and peer dialogues.",
            'category'     => 'enrichment',
            'priority'     => 'low',
            'guardian_tip' => "Support essay writing practice.",
            'student_tip'  => "Write essays on cultural topics.",
        ],
        4 => [
            'activity'     => "Filipino: Excellent. Promote advanced literature study and cultural projects.",
            'category'     => 'leadership',
            'priority'     => 'low',
            'guardian_tip' => "Encourage participation in cultural projects.",
            'student_tip'  => "Lead group discussions on Filipino literature.",
        ],
    ],
];
