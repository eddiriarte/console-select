<center>
  <h1>
    Console-Selection
  </h1>
  <img src="http://forthebadge.com/images/badges/gluten-free.svg">
  <img src="http://forthebadge.com/images/badges/built-by-developers.svg">
  <img src="http://forthebadge.com/images/badges/check-it-out.svg">
  
  <p>A fancy selection interface for symfony's console component.</p>
</center>

;) current working progress...

![Sample](docs/sample.gif)

## Install

```
composer require eddiriarte/console-select
```

## Usage

After installation register helper to your symfony's/laravel's/laravel-zero's command. Maybe in the constructor...

```php
# importing : \EddIriarte\Console\Helpers\SelectionHelper
# pasing the input and output interfaces
$this->getHelperSet()->set(
  new SelectionHelper($this->input, $this->output)
);
```

You could include a `select` method(in the command) as shorthand to acces helper: 

```php
# importing : \EddIriarte\Console\Inputs\CheckboxInput
#             \EddIriarte\Console\Inputs\CheckboxInput
public function select(string $message, array $options, bool $allowMultiple = true): array
{
    $helper = $this->getHelper('selection');
    $question = $allowMultiple ? new CheckboxInput($message, $options) : new RadioInput($message, $options);

    return $helper->select($question);
}
```

...thinking about put this stuff in a trait(??)


... more description will come soon!

## Still to do

Validations, Type-mapping, User-Interruptions, Code-Coverage, Release...



## MIT License

Copyright (c) 2018 Eduardo Iriarte

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.