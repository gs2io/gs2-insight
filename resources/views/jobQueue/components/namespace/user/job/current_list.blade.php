<div class="flex flex-col">
    <div class="-my-2 overflow-x-auto">
        <div class="py-2 align-middle inline-block min-w-full">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.jobQueue.job.name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.jobQueue.job.scriptId') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.jobQueue.job.args') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.model.jobQueue.job.retry') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($jobs as $job)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $job->job->getName() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $job->job->getScriptId() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @include('commons.arguments', ['args' => json_decode($job->job->getArgs(), true), 'ignoreFields' => []])
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $job->job->getCurrentRetryCount() }} / {{ $job->job->getMaxTryCount() }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{ $user->jobControllerView('jobQueue/components/namespace/user/job/controller') }}
