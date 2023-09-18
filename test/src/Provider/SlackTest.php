<?php
namespace Chadhutchins\OAuth2\Client\Test\Provider;

use Chadhutchins\OAuth2\Client\Provider\Exception\SlackProviderException;
use Chadhutchins\OAuth2\Client\Provider\Slack;
use Chadhutchins\OAuth2\Client\Provider\SlackResourceOwner;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class SlackTest extends TestCase
{
    protected $provider;

    protected static function getMethod($name)
    {
        $class = new ReflectionClass('Chadhutchins\OAuth2\Client\Provider\Slack');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    protected function setUp(): void
    {
        $this->provider = new Slack([
            'clientId'      => 'mock_client_id',
            'clientSecret'  => 'mock_secret',
            'redirectUri'   => 'none',
        ]);
    }

    public function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }

    public function testGetResourceOwnerDetailsUrl()
    {
        $token = m::mock('League\OAuth2\Client\Token\AccessToken', [['access_token' => 'mock_access_token']]);
        $token->shouldReceive('__toString')->andReturn('mock_access_token');

        $url = $this->provider->getResourceOwnerDetailsUrl($token);
        $uri = parse_url($url);

        $this->assertEquals('/api/openid.connect.userInfo', $uri['path']);
    }

    public function testGetAuthorizationUrl()
    {
        $params = [];
        $url = $this->provider->getAuthorizationUrl($params);
        $uri = parse_url($url);

        $this->assertEquals('/openid/connect/authorize', $uri['path']);
    }

    public function testGetBaseAccessTokenUrl()
    {
        $params = [];
        $url = $this->provider->getBaseAccessTokenUrl($params);
        $uri = parse_url($url);
        $this->assertEquals('/api/openid.connect.token', $uri['path']);
    }

    public function testGetAccessToken()
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['content-type' => 'json'],
                '{"access_token": "mock_access_token", "expires_in": 3600}'
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->provider->setHttpClient($client);
        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $this->assertEquals('mock_access_token', $token->getToken());
        $this->assertLessThanOrEqual(time() + 3600, $token->getExpires());
        $this->assertGreaterThanOrEqual(time(), $token->getExpires());
        $this->assertNull($token->getRefreshToken());
        $this->assertNull($token->getResourceOwnerId());
    }

    public function testCheckResponseThrowsIdentityProviderException()
    {
        $mock = new MockHandler([
            new Response(
                401,
                ['content-type' => 'json'],
                '{"ok": false, "error": "not_authed"}'
            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->provider->setHttpClient($client);

        $this->expectException(SlackProviderException::class);

        $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
    }

    public function testGetResourceOwnerDetails()
    {
        $body = <<<EOF
{
    "ok": true,
    "sub": "U0R7JM",
    "https://slack.com/user_id": "U0R7JM",
    "https://slack.com/team_id": "T0R7GR",
    "email": "krane@slack-corp.com",
    "email_verified": true,
    "date_email_verified": 1622128723,
    "name": "krane",
    "picture": "https://secure.gravatar.com/....png",
    "given_name": "Bront",
    "family_name": "Labradoodle",
    "locale": "en-US",
    "https://slack.com/team_name": "kraneflannel",
    "https://slack.com/team_domain": "kraneflannel",
    "https://slack.com/user_image_24": "...",
    "https://slack.com/user_image_32": "...",
    "https://slack.com/user_image_48": "...",
    "https://slack.com/user_image_72": "...",
    "https://slack.com/user_image_192": "...",
    "https://slack.com/user_image_512": "...",
    "https://slack.com/team_image_34": "...",
    "https://slack.com/team_image_44": "...",
    "https://slack.com/team_image_68": "...",
    "https://slack.com/team_image_88": "...",
    "https://slack.com/team_image_102": "...",
    "https://slack.com/team_image_132": "...",
    "https://slack.com/team_image_230": "...",
    "https://slack.com/team_image_default": true
}
EOF;
        $mock = new MockHandler([
            new Response(
                200,
                ['content-type' => 'application/x-www-form-urlencoded'],
                'access_token=mock_access_token&expires=3600&refresh_token=mock_refresh_token&otherKey={1234}'
            ),
            new Response(
                200,
                ['content-type' => 'json'],
                $body            ),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);
        $user = $this->provider->getResourceOwner($token);

        $this->assertInstanceOf(SlackResourceOwner::class, $user);

        $this->assertEquals('U0R7JM', $user->getId());
        $this->assertEquals('U0R7JM', $user->toArray()['https://slack.com/user_id']);

        $this->assertIsString($user->getTeamId());
        $this->assertIsString($user->getEmail());
        $this->assertIsBool($user->isEmailVerified());
        $this->assertIsInt($user->getDateEmailVerified());
        $this->assertIsString($user->getName());
        $this->assertIsString($user->getPicture());
        $this->assertIsString($user->getGivenName());
        $this->assertIsString($user->getFamilyName());
        $this->assertIsString($user->getLocale());
        $this->assertIsString($user->getTeamName());
        $this->assertIsString($user->getTeamDomain());
        $this->assertIsString($user->getUserImage24px());
        $this->assertIsString($user->getUserImage32px());
        $this->assertIsString($user->getUserImage48px());
        $this->assertIsString($user->getUserImage72px());
        $this->assertIsString($user->getUserImage192px());
        $this->assertIsString($user->getUserImage512px());
        $this->assertNull($user->getUserImage1024px());
        $this->assertIsString($user->getTeamImage34px());
        $this->assertIsString($user->getTeamImage44px());
        $this->assertIsString($user->getTeamImage68px());
        $this->assertIsString($user->getTeamImage88px());
        $this->assertIsString($user->getTeamImage102px());
        $this->assertIsString($user->getTeamImage132px());
        $this->assertIsString($user->getTeamImage230px());
        $this->assertIsBool($user->isTeamImageDefault());
    }
}