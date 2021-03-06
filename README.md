# MysqlClient Component

[![Latest Version](https://img.shields.io/github/release/ThrusterIO/mysql-client.svg?style=flat-square)]
(https://github.com/ThrusterIO/mysql-client/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)]
(LICENSE)
[![Build Status](https://img.shields.io/travis/ThrusterIO/mysql-client.svg?style=flat-square)]
(https://travis-ci.org/ThrusterIO/mysql-client)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/ThrusterIO/mysql-client.svg?style=flat-square)]
(https://scrutinizer-ci.com/g/ThrusterIO/mysql-client)
[![Quality Score](https://img.shields.io/scrutinizer/g/ThrusterIO/mysql-client.svg?style=flat-square)]
(https://scrutinizer-ci.com/g/ThrusterIO/mysql-client)
[![Total Downloads](https://img.shields.io/packagist/dt/thruster/mysql-client.svg?style=flat-square)]
(https://packagist.org/packages/thruster/mysql-client)

[![Email](https://img.shields.io/badge/email-team@thruster.io-blue.svg?style=flat-square)]
(mailto:team@thruster.io)

The Thruster MysqlClient Component.


## Install

Via Composer

```bash
$ composer require thruster/mysql-client
```

## Usage

```php
use Thruster\Component\MysqlClient\Client;
use Thruster\Component\MysqlClient\ConnectionPool;
use Thruster\Component\EventLoop\EventLoop;

$loop = new EventLoop();

$connectionPool = new ConnectionPool(function () {
    return new mysqli('127.0.0.1', 'root', '', 'timeverz');
});

$client = new Client($loop, $connectionPool);

for ($i = 0; $i < 100; $i++) {
    $client->query('SELECT * FROM users;')->then(
        function (\mysqli_result $result) use ($i) {
            foreach ($result->fetch_all(MYSQLI_ASSOC) as $item) {
                echo $i . ': ' . $item['id'] . PHP_EOL;
            }
        },
        function ($error) {
            // TODO: Handle error
        }
    );
}

$loop->run();
```

Result:

```
0: 1
0: 2
1: 1
1: 2
3: 1
3: 2
7: 1
7: 2
15: 1
15: 2
31: 1
31: 2
63: 1
63: 2
64: 1
64: 2
32: 1
32: 2
65: 1
65: 2
66: 1
66: 2
16: 1
16: 2
33: 1
33: 2
67: 1
67: 2
68: 1
68: 2
34: 1
34: 2
69: 1
69: 2
70: 1
70: 2
8: 1
8: 2
17: 1
17: 2
35: 1
35: 2
71: 1
71: 2
72: 1
72: 2
36: 1
36: 2
73: 1
73: 2
74: 1
74: 2
18: 1
18: 2
37: 1
37: 2
75: 1
75: 2
76: 1
76: 2
38: 1
38: 2
77: 1
77: 2
78: 1
78: 2
4: 1
4: 2
9: 1
9: 2
19: 1
19: 2
39: 1
39: 2
79: 1
79: 2
80: 1
80: 2
40: 1
40: 2
81: 1
81: 2
82: 1
82: 2
20: 1
20: 2
41: 1
41: 2
83: 1
83: 2
84: 1
84: 2
42: 1
42: 2
85: 1
85: 2
86: 1
86: 2
10: 1
10: 2
21: 1
21: 2
43: 1
43: 2
87: 1
87: 2
88: 1
88: 2
44: 1
44: 2
89: 1
89: 2
90: 1
90: 2
22: 1
22: 2
45: 1
45: 2
91: 1
91: 2
92: 1
92: 2
46: 1
46: 2
93: 1
93: 2
94: 1
94: 2
2: 1
2: 2
5: 1
5: 2
11: 1
11: 2
23: 1
23: 2
47: 1
47: 2
95: 1
95: 2
96: 1
96: 2
48: 1
48: 2
97: 1
97: 2
98: 1
98: 2
24: 1
24: 2
49: 1
49: 2
99: 1
99: 2
50: 1
50: 2
12: 1
12: 2
25: 1
25: 2
51: 1
51: 2
52: 1
52: 2
26: 1
26: 2
53: 1
53: 2
54: 1
54: 2
6: 1
6: 2
13: 1
13: 2
27: 1
27: 2
55: 1
55: 2
56: 1
56: 2
28: 1
28: 2
57: 1
57: 2
58: 1
58: 2
14: 1
14: 2
29: 1
29: 2
59: 1
59: 2
60: 1
60: 2
30: 1
30: 2
61: 1
61: 2
62: 1
62: 2
```

## Testing

```bash
$ composer test
```


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.


## License

Please see [License File](LICENSE) for more information.
