#!/usr/bin/env bash

# NAME
#     install.sh - Create the test fixture.
#
# SYNOPSIS
#     install.sh
#
# DESCRIPTION
#     Creates the test fixture.

cd "$(dirname "$0")" || exit; source _includes.sh

assert_env_vars

case "$ORCA_JOB" in
  "DEPRECATED_CODE_SCAN") orca debug:packages; eval "orca fixture:init -f --sut=$ORCA_SUT_NAME --sut-only --no-site-install" ;;
  "DEPRECATED_CODE_SCAN_CONTRIB") orca debug:packages; eval "orca fixture:init -f --no-site-install" ;;
  "ISOLATED_RECOMMENDED") orca debug:packages CURRENT_RECOMMENDED; eval "orca fixture:init -f --sut=$ORCA_SUT_NAME --sut-only --core=CURRENT_RECOMMENDED --profile=$ORCA_FIXTURE_PROFILE" ;;
  "INTEGRATED_RECOMMENDED") orca debug:packages CURRENT_RECOMMENDED; eval "orca fixture:init -f --sut=$ORCA_SUT_NAME --core=CURRENT_RECOMMENDED --profile=$ORCA_FIXTURE_PROFILE" ;;
  "CORE_PREVIOUS") orca debug:packages PREVIOUS_RELEASE; eval "orca fixture:init -f --sut=$ORCA_SUT_NAME --core=PREVIOUS_RELEASE --profile=$ORCA_FIXTURE_PROFILE" ;;
  "ISOLATED_DEV") orca debug:packages CURRENT_DEV; eval "orca fixture:init -f --sut=$ORCA_SUT_NAME --sut-only --core=CURRENT_DEV --dev --profile=$ORCA_FIXTURE_PROFILE" ;;
  "INTEGRATED_DEV") orca debug:packages CURRENT_DEV; eval "orca fixture:init -f --sut=$ORCA_SUT_NAME --core=CURRENT_DEV --dev --profile=$ORCA_FIXTURE_PROFILE" ;;
  "CORE_NEXT") orca debug:packages NEXT_DEV; eval "orca fixture:init -f --sut=$ORCA_SUT_NAME --core=NEXT_DEV --dev --profile=$ORCA_FIXTURE_PROFILE" ;;
  "CUSTOM") eval "orca fixture:init -f --sut=$ORCA_SUT_NAME --profile=$ORCA_FIXTURE_PROFILE ${ORCA_CUSTOM_FIXTURE_INIT_ARGS:=}" ;;
esac