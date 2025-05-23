<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any exams.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('moderator');
    }

    /**
     * Determine whether the user can view a specific exam.
     */
    public function view(User $user, Exam $exam)
    {
        return $user->hasRole('moderator') &&
               $user->id === $exam->moderator_id &&
               $user->district_id === $exam->district_id;
    }

    /**
     * Determine whether the user can create exams.
     */
    public function create(User $user)
    {
        return $user->hasRole('moderator');
    }

    /**
     * Determine whether the user can update the exam.
     */
    public function update(User $user, Exam $exam)
    {
        return $this->view($user, $exam);
    }

    /**
     * Determine whether the user can delete the exam.
     */
    public function delete(User $user, Exam $exam)
    {
        return $this->view($user, $exam);
    }

    /**
     * Determine whether the user can assign questions to the exam.
     */
    public function assignQuestions(User $user, Exam $exam)
    {
        return $this->view($user, $exam);
    }

    /**
     * Determine whether the user can select questions for the exam.
     */
    public function selectQuestions(User $user, Exam $exam)
    {
        return $this->view($user, $exam);
    }

    /**
     * Determine whether the moderator can release the exam.
     */
    public function release(User $user, Exam $exam)
    {
        return $user->hasRole('moderator') &&
               $user->id === $exam->moderator_id &&
               $user->district_id === $exam->district_id;
    }

    /**
     * Determine whether the user can modify questions (assign/unassign) for the exam.
     */
    public function modifyQuestions(User $user, Exam $exam)
    {
        return !$exam->is_released &&
               $user->hasRole('moderator') &&
               $user->district_id === $exam->district_id;
    }

    /**
     * Determine whether the student can take a mock exam.
     */
    public function takeMock(User $user, Exam $exam)
    {
        return $user->hasRole('student') && $exam->type === 'mock';
    }

    /**
     * Determine whether the student can apply for a scheduled exam.
     */
    public function apply(User $user, Exam $exam)
    {
        return $user->hasRole('student') &&
               $exam->type === 'scheduled' &&
               $exam->canApply(); // Assumes `canApply()` is defined in the Exam model
    }
}
