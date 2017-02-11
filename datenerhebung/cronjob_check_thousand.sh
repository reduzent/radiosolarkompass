#!/bin/bash
cd "$( dirname "${BASH_SOURCE[0]}" )"
for i in {1..1000}
do
  ./radio-tester.sh
done
