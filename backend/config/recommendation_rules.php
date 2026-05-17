<?php
// config/recommendation_rules.php

return [

    'weeks' => [
        1 => [
            'math' => [
                'weekly_activity' => [
                    'activity' => "Count toys during playtime.",
                ],
                'rating_rules' => [
                    0 => [
                        'activity'     => "No math class attended. Encourage playful counting at home.",
                        'narrative'    => "Cj missed Math this week, so playful counting at home is encouraged.",
                        'guardian_tip' => "Count fruits, toys, or steps together during daily routines.",
                        'student_tip'  => "Let’s count our toys together!",
                    ],
                    1 => [
                        'activity'     => "Needs Attention. Practice sorting shapes and colors.",
                        'narrative'    => "Math rating Needs Attention. Sorting shapes and colors helps strengthen recognition.",
                        'guardian_tip' => "Guide your child to group toys by color or size.",
                        'student_tip'  => "Can you find all the red blocks?",
                    ],
                    2 => [
                        'activity'     => "Good. Reinforce with simple counting games.",
                        'narrative'    => "Math rating Good. Reinforcing with simple counting games builds steady progress.",
                        'guardian_tip' => "Play board games or clap while counting steps.",
                        'student_tip'  => "Let’s count while we clap our hands.",
                    ],
                    3 => [
                        'activity'     => "Very Good. Introduce puzzles and number songs.",
                        'narrative'    => "Math rating Very Good. Puzzles and songs extend learning through play.",
                        'guardian_tip' => "Sing counting songs and provide simple puzzles.",
                        'student_tip'  => "Sing ‘One, Two, Buckle My Shoe’ with me!",
                    ],
                    4 => [
                        'activity'     => "Excellent. Encourage playful teaching of peers.",
                        'narrative'    => "Math rating Excellent. Child shows mastery and can share skills with peers.",
                        'guardian_tip' => "Let your child show siblings how to count toys.",
                        'student_tip'  => "Teach your friend how to count blocks!",
                    ],
                ],
            ],

            'science' => [
                'weekly_activity' => [
                    'activity' => "Water play experiment (sink/float).",
                ],
                'rating_rules' => [
                    0 => [
                        'activity'     => "No science class attended. Explore nature at home.",
                        'narrative'    => "Cj missed Science this week, so nature walks at home are encouraged.",
                        'guardian_tip' => "Take short walks and talk about plants and animals.",
                        'student_tip'  => "Let’s look at the leaves outside!",
                    ],
                    1 => [
                        'activity'     => "Needs Attention. Try simple water play experiments.",
                        'narrative'    => "Science rating Needs Attention. Water play builds curiosity and confidence.",
                        'guardian_tip' => "Use cups and toys in water to show sink or float.",
                        'student_tip'  => "Does this toy sink or float?",
                    ],
                    2 => [
                        'activity'     => "Good. Keep a simple observation journal.",
                        'narrative'    => "Science rating Good. Journaling observations helps steady progress.",
                        'guardian_tip' => "Encourage drawing what your child sees outdoors.",
                        'student_tip'  => "Draw the sun and clouds you see today.",
                    ],
                    3 => [
                        'activity'     => "Very Good. Explore with guided discovery play.",
                        'narrative'    => "Science rating Very Good. Guided discovery builds curiosity.",
                        'guardian_tip' => "Support curiosity by asking ‘What do you think will happen?’",
                        'student_tip'  => "Let’s guess what will happen if we mix colors!",
                    ],
                    4 => [
                        'activity'     => "Excellent. Encourage independent exploration.",
                        'narrative'    => "Science rating Excellent. Child explores independently with confidence.",
                        'guardian_tip' => "Provide safe materials for simple experiments.",
                        'student_tip'  => "Try exploring with your magnifying glass!",
                    ],
                ],
            ],

            'english' => [
                'weekly_activity' => [
                    'activity' => "Storytelling – The Three Little Pigs.",
                ],
                'rating_rules' => [
                    0 => [
                        'activity'     => "No English class attended. Encourage daily story time.",
                        'narrative'    => "Cj missed English this week, so bedtime story reading is encouraged.",
                        'guardian_tip' => "Read picture books together before bedtime.",
                        'student_tip'  => "Let’s read a story together tonight!",
                    ],
                    1 => [
                        'activity'     => "Needs Attention. Practice naming pictures in books.",
                        'narrative'    => "English rating Needs Attention. Naming pictures builds vocabulary.",
                        'guardian_tip' => "Point to pictures and ask, ‘What is this?’",
                        'student_tip'  => "Can you say the name of this animal?",
                    ],
                    2 => [
                        'activity'     => "Good. Reinforce with songs and rhymes.",
                        'narrative'    => "English rating Good. Songs and rhymes reinforce language skills.",
                        'guardian_tip' => "Sing nursery rhymes daily with your child.",
                        'student_tip'  => "Let’s sing ‘Twinkle Twinkle Little Star’ together!",
                    ],
                    3 => [
                        'activity'     => "Very Good. Encourage storytelling and role play.",
                        'narrative'    => "English rating Very Good. Storytelling builds imagination and confidence.",
                        'guardian_tip' => "Ask your child to retell a favorite story.",
                        'student_tip'  => "Tell me the story of the Three Little Pigs!",
                    ],
                    4 => [
                        'activity'     => "Excellent. Promote peer storytelling.",
                        'narrative'    => "English rating Excellent. Child confidently shares stories with peers.",
                        'guardian_tip' => "Encourage your child to share stories with friends.",
                        'student_tip'  => "Share your favorite story with your classmates!",
                    ],
                ],
            ],

            'filipino' => [
                'weekly_activity' => [
                    'activity' => "Sing Bahay Kubo and name vegetables.",
                ],
                'rating_rules' => [
                    0 => [
                        'activity'     => "No Filipino class attended. Encourage cultural songs and rhymes.",
                        'narrative'    => "Cj missed Filipino this week, so cultural songs at home are encouraged.",
                        'guardian_tip' => "Sing Filipino lullabies or folk songs at home.",
                        'student_tip'  => "Let’s sing ‘Bahay Kubo’ together!",
                    ],
                    1 => [
                        'activity'     => "Needs Attention. Practice simple Filipino words.",
                        'narrative'    => "Filipino rating Needs Attention. Practicing simple words builds vocabulary.",
                        'guardian_tip' => "Use flashcards with basic Filipino vocabulary.",
                        'student_tip'  => "Can you say ‘aso’ for dog?",
                    ],
                    2 => [
                        'activity'     => "Good. Reinforce with short dialogues.",
                        'narrative'    => "Filipino rating Good. Short dialogues encourage steady progress.",
                        'guardian_tip' => "Encourage speaking Filipino during playtime.",
                        'student_tip'  => "Let’s talk in Filipino while we play!",
                    ],
                    3 => [
                        'activity'     => "Very Good. Encourage storytelling in Filipino.",
                        'narrative'    => "Filipino rating Very Good. Storytelling builds fluency and confidence.",
                        'guardian_tip' => "Ask your child to tell a short story in Filipino.",
                        'student_tip'  => "Tell me a story in Filipino about your toy.",
                    ],
                    4 => [
                        'activity'     => "Excellent. Promote cultural sharing.",
                        'narrative'    => "Filipino rating Excellent. Child confidently shares culture with peers.",
                        'guardian_tip' => "Encourage participation in cultural activities.",
                        'student_tip'  => "Teach your classmates a Filipino song!",
                    ],
                ],
            ],
        ],
    ],
];
